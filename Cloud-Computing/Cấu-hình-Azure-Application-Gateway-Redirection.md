---
title: "Cấu hình Azure Application Gateway Redirection"
date: 2018-01-04 09:51:54
categories: [Cloud Computing, Azure]
---

Trong bài này, tôi sẽ hướng dẫn các bạn cách cấu hình Azure Application Gateway để có thể tự động redirect traffic HTTP sang HTTPS để đảm bảo tất cả giao tiếp giữa ứng dụng bên trong Azure với user của ứng dụng đều được mã hóa và thật sự đáng tin cậy. Trước hết, hãy xem tại sao cần phải Redirect HTTP sang HTTPS. ![](https://cloudcraft.info/wp-content/uploads/2018/01/cau-hinh-azure-application-gateway-redirection-1.png) Như scenario ở trên, users khi vào trình duyệt truy cập vào web của bạn, sẽ không quan tâm HTTP hay HTTPS, mà chỉ truy cập thông qua yourdomain.com.vn. Khi đó mặc định trình duyệt sẽ request HTTP (80), App Gateway cần được cấu hình để gửi lại thông tin redirection một cách tự động như 301 (temporary redirect) hoặc 302 (permanent redirect). Khi đó việc truy cập này sẽ tự động chuyển sang HTTPS, và người dùng sẽ không phải làm thêm bất kì một thao tác nào khác để truy cập vào web. 

## Redirect HTTP sang HTTPS trên Application Gateway có sẵn

Cấu hình bằng Powershell: 
    
    
    $AppGatewayName = "YourAppGateway"
    $ResourceGroupName = "YourResourceGroup"
    # Get the application gateway
    $gw = Get-AzureRmApplicationGateway -Name $AppGatewayName -ResourceGroupName $ResourceGroupName
    
    $frontendIP = "appGatewayFrontendIP"
    $frontendHTTPPort = "appGatewayFrontendPort"
    $hostname = "yourhostname.com"
    $listener443 = "Listener443-yourhostname.com"
    $listener80 = "Listener80-yourhostname.com"
    $redirectName = "redirectHttptoHttps-yourhostname.com"
    $ruleName = "RuleRedirect-yourhostname.com"
    
    # Get the existing HTTPS listener port 443
    $httpslistener = Get-AzureRmApplicationGatewayHttpListener -Name $listener443 -ApplicationGateway $gw
    
    # Get the existing front end IP configuration
    $fipconfig = Get-AzureRmApplicationGatewayFrontendIPConfig -Name $frontendIP -ApplicationGateway $gw
    
    # Add a new front end port to support HTTP traffic
    # Khong can cai nay:   Add-AzureRmApplicationGatewayFrontendPort -Name appGatewayFrontendPort2  -Port 80 -ApplicationGateway $gw
    
    # Get the recently created port [appGatewayFrontendPort (80), HTTPS-FrontEndPort (443)]
    $fp = Get-AzureRmApplicationGatewayFrontendPort -Name $frontendHTTPPort -ApplicationGateway $gw
    
    
    # Create a new HTTP listener using the port created earlier
    Add-AzureRmApplicationGatewayHttpListener -Name $listener80  -Protocol Http -FrontendPort $fp -FrontendIPConfiguration $fipconfig -HostName $hostname -ApplicationGateway $gw
    
    # Get the new listener
    $listener = Get-AzureRmApplicationGatewayHttpListener -Name $listener80 -ApplicationGateway $gw
    
    # Add a redirection configuration using a permanent redirect and targeting the existing listener
    Add-AzureRmApplicationGatewayRedirectConfiguration -Name $redirectName -RedirectType Permanent -TargetListener $httpslistener -IncludePath $true -IncludeQueryString $true -ApplicationGateway $gw
    
    # Get the redirect configuration
    $redirectconfig = Get-AzureRmApplicationGatewayRedirectConfiguration -Name $redirectName -ApplicationGateway $gw
    
    
    # Add a new rule to handle the redirect and use the new listener
    Add-AzureRmApplicationGatewayRequestRoutingRule -Name $ruleName -RuleType Basic -HttpListener $listener -RedirectConfiguration $redirectconfig -ApplicationGateway $gw
    
    # Update the application gateway
    Set-AzureRmApplicationGateway -ApplicationGateway $gw

## Redirect HTTP sang HTTPS dựa trên path
    
    
    $AppGatewayName = "YourAppGateway"
    $ResourceGroupName = "YourResourceGroup"
    
    # Get the application gateway
    $gw = Get-AzureRmApplicationGateway -Name $AppGatewayName -ResourceGroupName $ResourceGroupName
    
    # Get the existing HTTPS listener
    $httpslistener = Get-AzureRmApplicationGatewayHttpListener -Name appgatewayhttplistener -ApplicationGateway $gw
    
    # Get the existing front end IP configuration
    $fipconfig = Get-AzureRmApplicationGatewayFrontendIPConfig -Name appgatewayfrontendip -ApplicationGateway $gw
    
    # Add a new front end port to support HTTP traffic
    Add-AzureRmApplicationGatewayFrontendPort -Name appGatewayFrontendPort2  -Port 80 -ApplicationGateway $gw
    
    # Get the recently created port
    $fp = Get-AzureRmApplicationGatewayFrontendPort -Name appGatewayFrontendPort2 -ApplicationGateway $gw
    
    # Create a new HTTP listener using the port created earlier
    Add-AzureRmApplicationGatewayHttpListener -Name appgatewayhttplistener2  -Protocol Http -FrontendPort $fp -FrontendIPConfiguration $fipconfig -ApplicationGateway $gw 
    
    # Get the new listener
    $listener = Get-AzureRmApplicationGatewayHttpListener -Name appgatewayhttplistener2 -ApplicationGateway $gw
    
    # Add a redirection configuration using a permanent redirect and targeting the existing listener
    $redirectconfig = Get-AzureRmApplicationGatewayRedirectConfiguration -Name redirectpath6 -ApplicationGateway $gw
    
    # Retrieve the existing backend http settings to be used
    $poolSetting = Get-AzureRmApplicationGatewayBackendHttpSettings -Name "appGatewayBackendHttpSettings" -ApplicationGateway $gw
    
    # Retrieve an existing backend pool
    $pool = Get-AzureRmApplicationGatewayBackendAddressPool -Name appGatewayBackendPool -ApplicationGateway $gw
    
    # Create a new path rule for the path map configuration
    $pathRule = New-AzureRmApplicationGatewayPathRuleConfig -Name "pathrule6" -Paths "/image/*" -RedirectConfiguration $redirectconfig
    
    # Create a path map to add to the rule
    Add-AzureRmApplicationGatewayUrlPathMapConfig -Name "urlpathmap" -PathRules $pathRule -DefaultBackendAddressPool $pool -DefaultBackendHttpSettings $poolSetting -ApplicationGateway $gw
    
    # Retrieve the url path map created
    $urlPathMap = Get-AzureRmApplicationGatewayUrlPathMapConfig -Name "urlpathmap" -ApplicationGateway $gw
    
    # Add a new rule to handle the redirect and use the new listener
    Add-AzureRmApplicationGatewayRequestRoutingRule -Name "rule6" -RuleType PathBasedRouting -HttpListener $listener -UrlPathMap $urlPathMap -ApplicationGateway $gw
    
    # Update the application gateway
    Set-AzureRmApplicationGateway -ApplicationGateway $gw

## Redirect HTTP sang HTTPS cho multi-site
    
    
    # Create a new resource group for the application gateway
    New-AzureRmResourceGroup -Name appgw-rg -Location "Southeast Asia"
    
    # Create a subnet configuration object for the application gateway subnet. A subnet for an application should have a minimum of 28 mask bits. This value leaves 10 available addresses in the subnet for Application Gateway instances. With a smaller subnet, you may not be able to add more instance of your application gateway in the future.
    $gwSubnet = New-AzureRmVirtualNetworkSubnetConfig -Name 'appgwsubnet' -AddressPrefix 10.0.0.0/24
    
    # Create a subnet configuration object for the backend pool members subnet
    $nicSubnet = New-AzureRmVirtualNetworkSubnetConfig  -Name 'appsubnet' -AddressPrefix 10.0.2.0/24
    
    # Create the virtual network with the previous created subnets
    $vnet = New-AzureRmvirtualNetwork -Name 'appgwvnet' -ResourceGroupName appgw-rg -Location "Southeast Asia" -AddressPrefix 10.0.0.0/16 -Subnet $gwSubnet, $nicSubnet
    
    # Create a public IP address for use with the application gateway. Defining the domainnamelabel during creation is not supported for use with application gateway
    $publicip = New-AzureRmPublicIpAddress -ResourceGroupName appgw-rg -name 'appgwpip' -Location "Southeast Asia" -AllocationMethod Dynamic
    
    # Create a IP configuration. This configures what subnet the Application Gateway uses. When Application Gateway starts, it picks up an IP address from the subnet configured and routes network traffic to the IP addresses in the back-end IP pool.
    $gipconfig = New-AzureRmApplicationGatewayIPConfiguration -Name 'gwconfig' -Subnet $vnet.Subnets[0]
    
    # Create a backend pool to hold the addresses or NICs for the application that application gateway is protecting.
    $pool = New-AzureRmApplicationGatewayBackendAddressPool -Name 'pool01' -BackendIPAddresses 1.1.1.1, 2.2.2.2, 3.3.3.3
    
    # Conifugre the backend HTTP settings to be used to define how traffic is routed to the backend pool. The authenication certificate used in the previous step is added to the backend http settings.
    $poolSetting = New-AzureRmApplicationGatewayBackendHttpSettings -Name 'setting01' -Port 80 -Protocol Http -CookieBasedAffinity Enabled
    
    # Create a frontend port to be used by the listener.
    $fp = New-AzureRmApplicationGatewayFrontendPort -Name 'port01'  -Port 80
    
    # Create a frontend IP configuration to associate the public IP address with the application gateway
    $fipconfig = New-AzureRmApplicationGatewayFrontendIPConfig -Name 'fip01' -PublicIPAddress $publicip
    
    # Create the HTTP listener for the application gateway for "yourhostname.com". Assign the front-end ip configuration, and port.
    $listener1 = New-AzureRmApplicationGatewayHttpListener -Name listener01 -Protocol Http -FrontendIPConfiguration $fipconfig -FrontendPort $fp -HostName "yourhostname.com"
    
    # Create the HTTP listener for the application gateway for "yourhostname.org" this listener will redirect to the abc.com listener. Assign the front-end ip configuration, and port.
    $listener2 = New-AzureRmApplicationGatewayHttpListener -Name listener02 -Protocol Http -FrontendIPConfiguration $fipconfig -FrontendPort $fp -HostName "yourhostname.org"
    
    # Create the redirect configuration that will point traffic to the 
    $redirectconfig = New-AzureRmApplicationGatewayRedirectConfiguration -Name redirectOrgtoCom -RedirectType Found -TargetListener $listener1 -IncludePath $true -IncludeQueryString $true
    
    #Create a load balancer routing rule that configures the load balancer behavior. In this example, a basic round robin rule is created.
    $rule1 = New-AzureRmApplicationGatewayRequestRoutingRule -Name rule01 -RuleType Basic -HttpListener $listener1 -BackendHttpSettings $poolSetting -BackendAddressPool $pool
    
    #Create a load balancer routing rule that redirects traffic to the "yourhostname.com" listener
    $rule2 = New-AzureRmApplicationGatewayRequestRoutingRule -Name rule02 -RuleType Basic -HttpListener $listener2 -RedirectConfiguration $redirectconfig
    
    # Configure the SKU of the application gateway
    $sku = New-AzureRmApplicationGatewaySku -Name Standard_Medium -Tier Standard -Capacity 2
    
    # Create the application gateway utilizing all the previously created configuration objects
    $appgw = New-AzureRmApplicationGateway -Name appgwtest -ResourceGroupName appgw-rg -Location "Southeast Asia" -BackendAddressPools $pool -BackendHttpSettingsCollection $poolSetting -FrontendIpConfigurations $fipconfig  -GatewayIpConfigurations $gipconfig -FrontendPorts $fp -HttpListeners $listener,$listener2 -RequestRoutingRules $rule1,$rule2 -RedirectConfigurations $redirectconfig -Sku $sku

## Redirect sang một site khác

Phần cấu hình bên dưới dùng để cấu hình một traffic đi vào được redirect sang một site khác như anotherhostname.com. 
    
    
    # Create a new resource group for the application gateway
    New-AzureRmResourceGroup -Name appgw-rg -Location "Southeast Asia"
    
    # Create a subnet configuration object for the application gateway subnet. A subnet for an application should have a minimum of 28 mask bits. This value leaves 10 available addresses in the subnet for Application Gateway instances. With a smaller subnet, you may not be able to add more instance of your application gateway in the future.
    $gwSubnet = New-AzureRmVirtualNetworkSubnetConfig -Name 'appgwsubnet' -AddressPrefix 10.0.0.0/24
    
    # Create a subnet configuration object for the backend pool members subnet
    $nicSubnet = New-AzureRmVirtualNetworkSubnetConfig  -Name 'appsubnet' -AddressPrefix 10.0.2.0/24
    
    # Create the virtual network with the previous created subnets
    $vnet = New-AzureRmvirtualNetwork -Name 'appgwvnet' -ResourceGroupName appgw-rg -Location "Southeast Asia" -AddressPrefix 10.0.0.0/16 -Subnet $gwSubnet, $nicSubnet
    
    # Create a public IP address for use with the application gateway. Defining the domainnamelabel during creation is not supported for use with application gateway
    $publicip = New-AzureRmPublicIpAddress -ResourceGroupName appgw-rg -name 'appgwpip' -Location "Southeast Asia" -AllocationMethod Dynamic
    
    # Create a IP configuration. This configures what subnet the Application Gateway uses. When Application Gateway starts, it picks up an IP address from the subnet configured and routes network traffic to the IP addresses in the back-end IP pool.
    $gipconfig = New-AzureRmApplicationGatewayIPConfiguration -Name 'gwconfig' -Subnet $vnet.Subnets[0]
    
    # Create a backend pool to hold the addresses or NICs for the application that application gateway is protecting.
    $pool = New-AzureRmApplicationGatewayBackendAddressPool -Name 'pool01' -BackendIPAddresses 1.1.1.1, 2.2.2.2, 3.3.3.3
    
    # Conifugre the backend HTTP settings to be used to define how traffic is routed to the backend pool. The authenication certificate used in the previous step is added to the backend http settings.
    $poolSetting = New-AzureRmApplicationGatewayBackendHttpSettings -Name 'setting01' -Port 80 -Protocol Http -CookieBasedAffinity Enabled
    
    # Create a frontend port to be used by the listener.
    $fp = New-AzureRmApplicationGatewayFrontendPort -Name 'port01'  -Port 80
    
    # Create a frontend IP configuration to associate the public IP address with the application gateway
    $fipconfig = New-AzureRmApplicationGatewayFrontendIPConfig -Name 'fip01' -PublicIPAddress $publicip
    
    # Create the HTTP listener for the application gateway. Assign the front-end ip configuration, and port.
    $listener = New-AzureRmApplicationGatewayHttpListener -Name listener01 -Protocol Http -FrontendIPConfiguration $fipconfig -FrontendPort $fp 
    
    # Create the redirect configuration that will point traffic to the 
    $redirectconfig = New-AzureRmApplicationGatewayRedirectConfiguration -Name myredirect -RedirectType Temporary -TargetUrl "http://anotherdomain.com"
    
    #Create a load balancer routing rule that configures the load balancer behavior. In this example, a basic round robin rule is created.
    $rule = New-AzureRmApplicationGatewayRequestRoutingRule -Name rule01 -RuleType Basic -HttpListener $listener -RedirectConfiguration $redirectconfig 
    
    # Configure the SKU of the application gateway
    $sku = New-AzureRmApplicationGatewaySku -Name WAF_Medium -Tier WAF -Capacity 2
    
    # Create the application gateway utilizing all the previously created configuration objects
    $appgw = New-AzureRmApplicationGateway -Name appgwtest -ResourceGroupName appgw-rg -Location "Southeast Asia" -BackendAddressPools $pool -BackendHttpSettingsCollection $poolSetting -FrontendIpConfigurations $fipconfig  -GatewayIpConfigurations $gipconfig -FrontendPorts $fp -HttpListeners $listener -RequestRoutingRules $rule -RedirectConfigurations $redirectconfig -Sku $sku

 
