/**
 * *tabMsg.cityMap.repeatTabMsg.factory.js
 * 命名注释：server简称_tab. 父模块 dipan . 功能_获取后台数据,动态生成协议  类型_factory.js
 * Created by xuehuifang on 16/4/5.
 */
(function () {
    'use strict';
    angular.module('cityMap').factory('getTabData', getTabData);
    getTabData.$inject = ['$http','$q'];

    function getTabData($http,$q) {
        var service={};
        service.getHttpData=function(){             //获取后台数据,动态显示协议列表
            var deferred=$q.defer();
            $http.get('json_test.json').success(function(result){       //测试json
                deferred.resolve(result);
            })
                return deferred.promise;
        }

        service.MsgDisNonee = function(){
            document.getElementById("alertMsg").style.display="none";
        }
        service.MsgDisBlockk = function(){
            document.getElementById("alertMsg").style.display="block";
        }
        return service;
    }


})();
