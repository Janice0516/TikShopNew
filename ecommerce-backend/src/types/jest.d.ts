// Jest全局类型定义
declare global {
  function expect(actual: any): any;
  function describe(name: string, fn: () => void): void;
  function it(name: string, fn: () => void): void;
  function beforeEach(fn: () => void): void;
  function afterEach(fn: () => void): void;
  function beforeAll(fn: () => void): void;
  function afterAll(fn: () => void): void;
}

export {};
