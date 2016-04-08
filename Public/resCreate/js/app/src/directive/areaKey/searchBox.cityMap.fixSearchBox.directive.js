/**
 * searchBox.cityMap.fixSearchBox.directive
 * * 命名名称注释:directive 简称_searchBox,父模块_dipan . 功能_根据需求,可以组合选择符合搜索条件的协议. 类型_directive .js
 * 使用 ：<div search-box></div>
 * Created by xuehuifang on 16/4/7.
 */
(function () {
    'use strict';

    angular.module('cityMap').directive('searchBox', searchBox);
    function searchBox() {

        return {
            restrict: 'A',
            replace: false,
            scope: {},
            templateUrl: 'Public/resCreate/html/src/public/areaKey/searchBox.cityMap.fixSearchBox.directive.html',
            controller: thisController,
            link: function (scope, element, attrs) {

            }
        };
    }

    thisController.$inject = ['$scope','searchDataService'];

    function thisController($scope,searchDataService) {
        //点击时间单选框,是否送达单选框
           $scope.clickRadio = function(){
               var timer = searchDataService.getSearchTimeData();
               var arrive = searchDataService.getSearchArriveDAta();
               alert(timer);
               alert(arrive);
           }
        //输入司机电话号码
           $scope.driverOnBlur = function(){
               var driverPhone = searchDataService.getDriverPhone();
               alert(driverPhone);
           }

            $scope.masterOnBlur = function(){
                var masterName = searchDataService.getMasterName();
                alert(masterName);
            }
        $scope.updataTabByTime = function (){
            searchDataService.callTimeData().then(function(data){

            })
        }

    }


})();
