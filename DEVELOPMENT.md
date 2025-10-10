# 开发指南

本文档提供详细的开发流程和最佳实践。

## 📋 目录

1. [开发环境设置](#开发环境设置)
2. [开发流程](#开发流程)
3. [代码规范](#代码规范)
4. [模块开发](#模块开发)
5. [数据库操作](#数据库操作)
6. [测试](#测试)
7. [常见问题](#常见问题)

---

## 开发环境设置

### 推荐的开发工具

1. **VS Code** + 插件
   - ESLint
   - Prettier
   - GitLens
   - Thunder Client (API测试)
   - MySQL (Jun Han)

2. **Postman** - API测试

3. **Navicat** - 数据库管理

4. **Another Redis Desktop Manager** - Redis管理

### VS Code配置

创建 `.vscode/settings.json`:

```json
{
  "editor.formatOnSave": true,
  "editor.codeActionsOnSave": {
    "source.fixAll.eslint": true
  },
  "eslint.validate": [
    "javascript",
    "javascriptreact",
    "typescript",
    "typescriptreact"
  ]
}
```

---

## 开发流程

### 1. 创建新功能的标准流程

```bash
# Step 1: 创建功能分支
git checkout -b feature/product-management

# Step 2: 创建模块
cd ecommerce-backend
nest g module modules/product
nest g controller modules/product
nest g service modules/product

# Step 3: 开发功能
# - 创建Entity
# - 创建DTO
# - 实现Service
# - 实现Controller
# - 编写测试

# Step 4: 测试
npm run test
npm run start:dev  # 手动测试

# Step 5: 提交代码
git add .
git commit -m "feat: 添加商品管理功能"
git push origin feature/product-management

# Step 6: 合并到主分支
git checkout dev
git merge feature/product-management
```

### 2. Git提交规范

```bash
# 提交格式
<type>(<scope>): <subject>

# type类型
feat:     新功能
fix:      修复bug
docs:     文档更新
style:    代码格式（不影响功能）
refactor: 重构
test:     测试
chore:    构建/工具

# 示例
feat(user): 添加用户注册功能
fix(order): 修复订单金额计算错误
docs(readme): 更新安装说明
refactor(product): 重构商品查询逻辑
```

---

## 代码规范

### TypeScript规范

```typescript
// ✅ 好的实践
// 1. 明确的类型定义
interface UserInfo {
  id: number;
  phone: string;
  nickname: string;
}

async function getUser(id: number): Promise<UserInfo> {
  const user = await this.userRepository.findOne({ where: { id } });
  return {
    id: user.id,
    phone: user.phone,
    nickname: user.nickname,
  };
}

// 2. 使用解构
const { phone, password } = loginDto;

// 3. 使用可选链
const userName = user?.nickname || '默认昵称';

// 4. 避免any类型
// ❌ 不好
function process(data: any) {}
// ✅ 好
function process(data: UserDto) {}
```

### NestJS最佳实践

```typescript
// 1. 使用依赖注入
@Injectable()
export class UserService {
  constructor(
    @InjectRepository(User)
    private userRepository: Repository<User>,
    private jwtService: JwtService,
  ) {}
}

// 2. 使用DTO验证
import { IsString, IsNotEmpty, Length } from 'class-validator';

export class CreateUserDto {
  @IsString()
  @IsNotEmpty()
  @Length(11, 11)
  phone: string;
}

// 3. 统一异常处理
if (!user) {
  throw new HttpException('用户不存在', HttpStatus.NOT_FOUND);
}

// 4. 使用装饰器
@UseGuards(JwtAuthGuard)
@ApiOperation({ summary: '获取个人信息' })
@Get('profile')
async getProfile() {}
```

### 命名规范

```typescript
// 文件命名：kebab-case
user.controller.ts
user.service.ts
user.entity.ts
create-user.dto.ts

// 类命名：PascalCase
class UserController {}
class UserService {}
class CreateUserDto {}

// 函数/变量：camelCase
async getUser() {}
const userName = 'John';

// 常量：UPPER_SNAKE_CASE
const MAX_FILE_SIZE = 10485760;
const API_BASE_URL = 'https://api.example.com';

// 接口：PascalCase + I前缀（可选）
interface IUserInfo {}
// 或
interface UserInfo {}
```

---

## 模块开发

### 创建完整的模块示例

以商品模块为例：

#### 1. 创建Entity

```typescript
// src/modules/product/entities/product.entity.ts
import { Entity, Column, PrimaryGeneratedColumn } from 'typeorm';

@Entity('platform_product')
export class Product {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: number;

  @Column({ length: 200 })
  name: string;

  @Column({ name: 'category_id', type: 'bigint' })
  categoryId: number;

  @Column({ type: 'decimal', precision: 10, scale: 2 })
  costPrice: number;

  @Column({ type: 'int', default: 0 })
  stock: number;

  @Column({ type: 'tinyint', default: 1 })
  status: number;
}
```

#### 2. 创建DTO

```typescript
// src/modules/product/dto/create-product.dto.ts
import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsNumber, IsNotEmpty, Min } from 'class-validator';

export class CreateProductDto {
  @ApiProperty({ description: '商品名称' })
  @IsString()
  @IsNotEmpty()
  name: string;

  @ApiProperty({ description: '分类ID' })
  @IsNumber()
  @IsNotEmpty()
  categoryId: number;

  @ApiProperty({ description: '成本价' })
  @IsNumber()
  @Min(0)
  costPrice: number;

  @ApiProperty({ description: '库存' })
  @IsNumber()
  @Min(0)
  stock: number;
}
```

#### 3. 实现Service

```typescript
// src/modules/product/product.service.ts
import { Injectable, HttpException, HttpStatus } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Product } from './entities/product.entity';
import { CreateProductDto } from './dto/create-product.dto';

@Injectable()
export class ProductService {
  constructor(
    @InjectRepository(Product)
    private productRepository: Repository<Product>,
  ) {}

  // 创建商品
  async create(createProductDto: CreateProductDto) {
    const product = this.productRepository.create(createProductDto);
    return await this.productRepository.save(product);
  }

  // 查询商品列表
  async findAll(page = 1, pageSize = 10) {
    const [list, total] = await this.productRepository.findAndCount({
      where: { status: 1 },
      skip: (page - 1) * pageSize,
      take: pageSize,
      order: { id: 'DESC' },
    });

    return { list, total, page, pageSize };
  }

  // 查询商品详情
  async findOne(id: number) {
    const product = await this.productRepository.findOne({ 
      where: { id } 
    });
    
    if (!product) {
      throw new HttpException('商品不存在', HttpStatus.NOT_FOUND);
    }
    
    return product;
  }

  // 更新商品
  async update(id: number, updateProductDto: any) {
    await this.findOne(id); // 检查是否存在
    await this.productRepository.update(id, updateProductDto);
    return await this.findOne(id);
  }

  // 删除商品（软删除）
  async remove(id: number) {
    await this.findOne(id);
    await this.productRepository.update(id, { status: 0 });
    return { message: '删除成功' };
  }
}
```

#### 4. 实现Controller

```typescript
// src/modules/product/product.controller.ts
import {
  Controller,
  Get,
  Post,
  Body,
  Param,
  Put,
  Delete,
  Query,
  UseGuards,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { ProductService } from './product.service';
import { CreateProductDto } from './dto/create-product.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@ApiTags('商品模块')
@Controller('products')
export class ProductController {
  constructor(private readonly productService: ProductService) {}

  @Post()
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '创建商品' })
  create(@Body() createProductDto: CreateProductDto) {
    return this.productService.create(createProductDto);
  }

  @Get()
  @ApiOperation({ summary: '商品列表' })
  findAll(
    @Query('page') page: number = 1,
    @Query('pageSize') pageSize: number = 10,
  ) {
    return this.productService.findAll(page, pageSize);
  }

  @Get(':id')
  @ApiOperation({ summary: '商品详情' })
  findOne(@Param('id') id: number) {
    return this.productService.findOne(id);
  }

  @Put(':id')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '更新商品' })
  update(@Param('id') id: number, @Body() updateProductDto: any) {
    return this.productService.update(id, updateProductDto);
  }

  @Delete(':id')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '删除商品' })
  remove(@Param('id') id: number) {
    return this.productService.remove(id);
  }
}
```

#### 5. 创建Module

```typescript
// src/modules/product/product.module.ts
import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { ProductService } from './product.service';
import { ProductController } from './product.controller';
import { Product } from './entities/product.entity';

@Module({
  imports: [TypeOrmModule.forFeature([Product])],
  controllers: [ProductController],
  providers: [ProductService],
  exports: [ProductService],
})
export class ProductModule {}
```

---

## 数据库操作

### TypeORM常用操作

```typescript
// 1. 查询单条
const user = await this.userRepository.findOne({
  where: { id: 1 }
});

// 2. 查询多条
const users = await this.userRepository.find({
  where: { status: 1 },
  order: { id: 'DESC' },
  take: 10,
});

// 3. 查询并计数
const [list, total] = await this.userRepository.findAndCount({
  where: { status: 1 },
  skip: 0,
  take: 10,
});

// 4. 插入
const user = this.userRepository.create({ phone, password });
await this.userRepository.save(user);

// 5. 更新
await this.userRepository.update(id, { nickname: 'new name' });

// 6. 删除
await this.userRepository.delete(id);

// 7. 复杂查询
const users = await this.userRepository
  .createQueryBuilder('user')
  .where('user.status = :status', { status: 1 })
  .andWhere('user.phone LIKE :phone', { phone: '%138%' })
  .orderBy('user.id', 'DESC')
  .take(10)
  .getMany();

// 8. 关联查询
const orders = await this.orderRepository.find({
  relations: ['user', 'items'],
  where: { userId: 1 },
});

// 9. 事务
await this.userRepository.manager.transaction(async manager => {
  await manager.save(user);
  await manager.save(order);
});
```

---

## 测试

### 单元测试示例

```typescript
// user.service.spec.ts
import { Test, TestingModule } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';
import { UserService } from './user.service';
import { User } from './entities/user.entity';

describe('UserService', () => {
  let service: UserService;

  const mockUserRepository = {
    findOne: jest.fn(),
    save: jest.fn(),
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        UserService,
        {
          provide: getRepositoryToken(User),
          useValue: mockUserRepository,
        },
      ],
    }).compile();

    service = module.get<UserService>(UserService);
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });

  it('should find a user by id', async () => {
    const user = { id: 1, phone: '13800138000' };
    mockUserRepository.findOne.mockResolvedValue(user);

    expect(await service.findById(1)).toEqual(user);
    expect(mockUserRepository.findOne).toHaveBeenCalledWith({ 
      where: { id: 1 } 
    });
  });
});
```

---

## 常见问题

### 1. 数据库连接池耗尽

```typescript
// 确保在finally中释放连接
const connection = await this.connection.getConnection();
try {
  // 执行数据库操作
} finally {
  await connection.release();
}
```

### 2. 循环依赖

```typescript
// 使用forwardRef解决
@Module({
  imports: [forwardRef(() => UserModule)],
})
export class OrderModule {}
```

### 3. 大批量数据处理

```typescript
// 使用分批处理
async function processBatch() {
  const pageSize = 100;
  let page = 1;
  let hasMore = true;

  while (hasMore) {
    const data = await this.repository.find({
      skip: (page - 1) * pageSize,
      take: pageSize,
    });

    if (data.length === 0) {
      hasMore = false;
    } else {
      // 处理数据
      await this.process(data);
      page++;
    }
  }
}
```

---

## 下一步

- 阅读 [PROJECT.md](PROJECT.md) 了解完整架构
- 阅读 [RECOMMENDATIONS.md](RECOMMENDATIONS.md) 了解最佳实践
- 参考 `src/modules/user` 了解完整示例

---

最后更新：2025-10-04

