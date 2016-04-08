(function () {
    'use strict';

    /**
     * 从新动态绑定bindonce
     * 传入 需要绑定的元素id_str , 作用域_obj
     * ng-repeat 可不需要此方法，因为ngrepeat 本身就是执行了动态绑定
     * 16/2/1 */
    angular.module('cityMap').factory('repBindOnce', repBindOnce);
    repBindOnce.$inject = ['$compile'];

    function repBindOnce($compile) {
        function _repBindOnce(id, scope) {
            var content = document.getElementById(id);
            $compile(content)(scope);
        }

        return _repBindOnce;
    }


})();