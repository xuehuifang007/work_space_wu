'use strict';
describe('body', function () {
    var scope;

    /**
     * 模拟Dipan module 并注入 我们自己的依赖
     * 16/2/3 */
    beforeEach(angular.mock.module('cityMap'));

    /**
     * 模拟controller
     * 16/2/3 */
    beforeEach(angular.mock.inject(function ($rootScope, $controller) {

        /**
         * 创建一个空 scope
         * 16/2/3 */
        scope = $rootScope.$new();

        /**
         * 声明 Contreller 并且注入已经创建的空的 scope
         * 16/2/3 */
        $controller('body', {$scope: scope});

    }));

    /**
     * 测试
     * 16/2/3 */

    it('body测试', function () {
        expect(scope.aaabbb).toBe(1);
    })


});