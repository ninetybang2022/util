####简易路由类
> 只能在php5.5以上版本使用
```
  Router::setError404(function(){
    //404错误
  })
  //访问 /1 就会访问当下的方法
  Router::get('/(:num)',function($id){
    echo $id;
  });

  Router::run(); //运行
```
