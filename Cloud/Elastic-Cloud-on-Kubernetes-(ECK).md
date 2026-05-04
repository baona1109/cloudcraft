---
title: "Elastic Cloud on Kubernetes (ECK)"
date: 2023-01-09 14:28:39
categories: [Cloud Computing, Kubernetes]
---

Elastic Cloud on Kubernetes hay còn viết tắt là ECK là một operator được viết và phát triển bởi Elastic nhằm mục đích phục vụ cho việc cài đặt, điều khiển và cấu hình các cluster Elasticsearch trên 1 cluster Kubernetes. Có nhiều cách cài cluster Elasticsearch lên kubernetes như là sử dụng helm chart cũng do elastic cung cấp hoặc có thể tự viết helm chart riêng theo ý muốn rồi cài đặt hoặc đơn giản hơn là làm 1 bộ file manifest k8s rồi cứ kubectl apply nó là xong. Ngoài ra không thích thì vẫn có thể cài đặt trên các Instance thông thường như truyền thống. Vậy ECK có lợi ích gì mà mình giới thiệu nó? ![](http://cloudcraft.info/wp-content/uploads/2023/01/illustration-elastic-cloud-on-kubernetes.png)  

## Lợi ích

Về việc sử dụng manifest truyền thống thậm chí kết hợp kustomization hoặc helm cài lện k8s để quản lý thì nó vẫn rất khó khăn nếu sau này phình ra. Bạn sẽ đối diện với các vấn đề sau nếu dùng cách truyền thống: 

  * Dựng cluster ES trong 1 nốt nhạc
  * Nếu dự án hoặc công ty có nhiều dự án cần nhiều clusters Elasticsearch thì quản lý như thế nào?
  * Cấu hình nâng cao như chia nodeset, thay đổi type disk của node elasticsearch như thế nào?
  * Sử dụng các tính năng nâng cao khác như apm, heatbeat,...

Những liệt kê trên thì ECK có thể giải quyết được một cách dễ dàng 

## Cài đặt

Có 2 loại hình cài đặt 

  * Sử dụng manifest và tiến hành kubectl apply -f
  * Cài đặt qua helm chart (thường hay dùng cách này để dể quản lý và upgrade version)

1\. Cài đặt sử dụng manifest Đầu tiên ta cài đặt bộ CRDs cho nó để define các object cho api server của kubernetes 
    
    
    kubectl create -f https://download.elastic.co/downloads/eck/2.4.0/crds.yaml

Tiến hành cài đặt ECK 
    
    
    kubectl apply -f https://download.elastic.co/downloads/eck/2.4.0/operator.yaml

2\. Cài đặt sử dụng Helm Yêu cầu phải cài đặt command line helm Đầu tiên add helm repo vào local trên máy 
    
    
    helm repo add elastic https://helm.elastic.co
    helm repo update

Sau đó tiến hành cài đặt 
    
    
    helm install elastic-operator elastic/eck-operator -n elastic-system --create-namespace

Ở đây chỉ cài đặt ECK với cấu hình cơ bản theo cluster wide mode. Điều này có nghĩa ECK sẽ có thể tạo các cluster elasticsearch ở toàn bộ namespaces của kubernetes  

## Sử dụng

Sử dụng thì cũng đơn giản thôi và trên trang chủ của Elastic cũng có nói tới 
    
    
    apiVersion: elasticsearch.k8s.elastic.co/v1
    kind: Elasticsearch
    metadata:
      name: elasticsearch-logs
      namespace: elastic-system
    spec:
      version: 7.13.4
      volumeClaimDeletePolicy: DeleteOnScaledownOnly
      nodeSets:
      - name: logs-disk-gp3-xfs
        count: 3
        config:
          node.store.allow_mmap: false
        podTemplate:
          spec:
            initContainers:
            - name: sysctl
              securityContext:
                privileged: true
              command: ['sh', '-c', 'sysctl -w vm.max_map_count=262144']
            containers:
            - name: elasticsearch
              # specify resource limits and requests
              resources:
                requests:
                  memory: 4Gi
                  cpu: 1
                limits:
                  memory: 8Gi
                  cpu: 2
              env:
              - name: ES_JAVA_OPTS
                value: "-Xms2g -Xmx2g"
            nodeSelector:
              alpha.eksctl.io/nodegroup-name: spotsystemnodes
            tolerations:
              - key: "systemnodes"
                operator: "Exists"
                effect: "NoSchedule"
        volumeClaimTemplates:
        - metadata:
            name: elasticsearch-data
          spec:
            accessModes:
            - ReadWriteOnce
            resources:
              requests:
                storage: 100Gi
            storageClassName: gp3-xfs
      http:
        tls:
          selfSignedCertificate:
            disabled: true

Như trên là config Yaml để tạo được 1 cluster ES gồm 3 nodes thuộc nodeSet của ES có tên là **logs-gp3-xfs** ECK cũng hỗ trợ tốt trong việc change type storageclass hoặc tăng size disk. 

  * Khi tăng size disk thì chỉ cần update lại chỗ storage rồi tiến hành kubectl apply -f lại là được
  * Còn đổi storageclass thì buộc tạo nodeSet mới rồi rollout dần nodeSet cũ

Trong cấu hình trên chú ý: **volumeClaimDeletePolicy: DeleteOnScaledownOnly** config này giúp cho ECK sẽ không xóa PVC khi mà mình destroy cluster, nó chỉ delete disk khi nhận tính hiệu scaledown Ngoài ra còn nhiều config khác nữa các bạn tham khảo trên [documents](https://www.elastic.co/guide/en/cloud-on-k8s/current/index.html) trong quá trình sử dụng nhé 

## Tham Khảo

https://www.elastic.co/guide/en/cloud-on-k8s/current/index.html  
