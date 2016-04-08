(function () {
    'use strict';

    /**
     * body 控制器 老模板 里面的 controller
     * 16/2/1 */
    angular.module('cityMap').controller('cityBody', body);

    /**
     * body 控制器
     * 16/2/1 */
    angular.module('cityMap').controller('topHeader', topHeader);




    /**
     * 手动注入
     * 16/2/1 */
    body.$inject = ['$scope', '$timeout'];


    /**
     * controllerFun  此处是 bindOnce 用法., 如果 数据更新的话,需要调用 repBindOnce 方法从新绑定once ,这里是 demo
     * 16/2/1 */
    function body($scope, $timeout, repBindOnce) {

        $scope.aaabbb = 1;
        $timeout(function () {
            $scope.aaabbb = 11;
        }, 4000);
//        $scope.b = '';
//        $scope.c = [1, 2, 3.4];

//        var content = angualr.element('bindonce');

//        $timeout(function () {
//            $scope.b = 2222;
//            $scope.c = [4, 5, 6];
////            repBindOnce('bindonce', $scope);
//        }, 2000);
    };
    function topHeader(){

    };

    //angular.module('cityMap').factory('tabmsg',tabmsg);
    //    function tabmsg($scope){
    //        function aa(){
    //            $scope.name="kkkk";
    //        }
    //
    //
    //
    //    }
})();
