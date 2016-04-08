/**searchBox.cityMap.fixSearchBox.factory.js
 * 命名注释：server简称_searchDataService. 父模块 dipan . 功能_根据所选内容或者输入的内容检索协议  类型_factory.js
 * Created by xuehuifang on 16/4/7.
 */
(function () {
    'use strict';
    angular.module('cityMap').factory('searchDataService',searchDataService );
    searchDataService.$inject = ['$http','$q'];

    function searchDataService ($http,$q) {
        var service = {};

            //时间选中状态判断
            service.getSearchTimeData = function(){
                //获得 单选选按钮name集合
                var radios=document.getElementsByName("Time");
                    //根据 name集合长度 遍历name集合
                    for(var i = 0;i < radios.length;i++)
                    {
                        //判断那个单选按钮为选中状态
                        if(radios[i].checked)
                        {
                            var radioValue = radios[i].value;
                            return radioValue;
                        }
                    }
            }

            //是否选中送达状态判断
            service.getSearchArriveDAta = function(){
                var check = document.getElementsByName("condition");
                for (var i = 0;i < check.length;i++){
                    if(check[i].checked){
                        var checkValue = check[i].value;
                        return checkValue;
                    }
                }
            }

            //司机电话号码
            service.getDriverPhone = function(){
                var driverValue = document.getElementById("condition").value;
                return driverValue;
            }

            //收货人姓名
            service.getMasterName = function(){
                var masterNameValue = document.getElementById("condition1").value;
                return masterNameValue;
            }

            //传时间值到后台
            service.callTimeData = function(){
                var deferred = $q().defer();
                $http.get(' http://localhost/city/index.php/Member/Hdeal/deal_monitor/deal_status/'+driverValue).success(function(data){
                    deferred.resolve(data);
                }).error(function(){
                    deferred.reject("There was an error");
                })
                return deferred.promise;
            }

        return service;
    }
})();