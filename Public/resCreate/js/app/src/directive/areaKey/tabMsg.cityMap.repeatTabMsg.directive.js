/**
 * tabMsg.cityMap.repeatTabMsg.directive.js
 * 命名名称注释:directive 简称_tabMsg,父模块_dipan . 功能_显示协议列表. 类型_directive .js
 * 使用 ：<div tab-msg></div>
 * Created by xuehuifang on 16/4/5.
 */
(function () {
    'use strict';

    angular.module('cityMap').directive('tabMsg',tabMsg);
    function tabMsg() {

        return {
            restrict: 'A',
            replace: false,
            scope: {},
            templateUrl: 'Public/resCreate/html/src/public/areaKey/tabMsg.cityMap.repeatTabMsg.directive.html',
            controller: thisController,
            link: function (scope, element, attrs) {
               

            }
        };
    }

    thisController.$inject = ['$scope','getTabData'];
    //http://city.5656111.com/Member/Hdeal/deal_monitor?goods_id=9255 不能访问
    function thisController($scope,getTabData) {
        getTabData.getHttpData().then(function(data){
            $scope.lists=data.list;

            $scope.over = function(){
                getTabData.MsgDisNonee();
            }
            $scope.out = function(){
                getTabData.MsgDisBlockk();
            }
        })

    }

})();