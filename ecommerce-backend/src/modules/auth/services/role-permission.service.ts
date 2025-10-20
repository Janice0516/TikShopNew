import { Injectable, HttpException, HttpStatus } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, In } from 'typeorm';
import { Role } from '../entities/role.entity';
import { Permission } from '../entities/permission.entity';
import { CreateRoleDto, UpdateRoleDto, QueryRoleDto, CreatePermissionDto, UpdatePermissionDto } from '../dto/role-permission.dto';

@Injectable()
export class RolePermissionService {
  constructor(
    @InjectRepository(Role)
    private roleRepository: Repository<Role>,
    @InjectRepository(Permission)
    private permissionRepository: Repository<Permission>,
  ) {}

  // ========== 角色管理 ==========
  
  /**
   * 创建角色
   */
  async createRole(createRoleDto: CreateRoleDto) {
    const { permissionIds, ...roleData } = createRoleDto;

    // 检查角色代码是否已存在
    const existingRole = await this.roleRepository.findOne({ where: { code: roleData.code } });
    if (existingRole) {
      throw new HttpException('角色代码已存在', HttpStatus.BAD_REQUEST);
    }

    const role = this.roleRepository.create({
      ...roleData,
      status: 1,
      isSystem: 0,
    });

    // 如果有权限ID，关联权限
    if (permissionIds && permissionIds.length > 0) {
      const permissions = await this.permissionRepository.findBy({ id: In(permissionIds) });
      role.permissions = permissions;
    }

    return await this.roleRepository.save(role);
  }

  /**
   * 获取角色列表
   */
  async getRoles(query: QueryRoleDto) {
    const { page = 1, limit = 10, code, name, status } = query;
    const skip = (page - 1) * limit;

    const queryBuilder = this.roleRepository.createQueryBuilder('role')
      .leftJoinAndSelect('role.permissions', 'permissions');

    if (code) {
      queryBuilder.andWhere('role.code LIKE :code', { code: `%${code}%` });
    }
    if (name) {
      queryBuilder.andWhere('role.name LIKE :name', { name: `%${name}%` });
    }
    if (status !== undefined) {
      queryBuilder.andWhere('role.status = :status', { status });
    }

    const [list, total] = await queryBuilder
      .orderBy('role.createTime', 'DESC')
      .skip(skip)
      .take(limit)
      .getManyAndCount();

    return { list, total, page, limit };
  }

  /**
   * 更新角色
   */
  async updateRole(id: string, updateRoleDto: UpdateRoleDto) {
    const { permissionIds, ...roleData } = updateRoleDto;
    
    const role = await this.roleRepository.findOne({ 
      where: { id },
      relations: ['permissions']
    });
    
    if (!role) {
      throw new HttpException('角色不存在', HttpStatus.NOT_FOUND);
    }

    // 更新角色基本信息
    Object.assign(role, roleData);

    // 更新权限关联
    if (permissionIds !== undefined) {
      if (permissionIds.length > 0) {
        const permissions = await this.permissionRepository.findBy({ id: In(permissionIds) });
        role.permissions = permissions;
      } else {
        role.permissions = [];
      }
    }

    return await this.roleRepository.save(role);
  }

  /**
   * 删除角色
   */
  async deleteRole(id: string) {
    const role = await this.roleRepository.findOne({ where: { id } });
    if (!role) {
      throw new HttpException('角色不存在', HttpStatus.NOT_FOUND);
    }

    if (role.isSystem === 1) {
      throw new HttpException('系统角色不能删除', HttpStatus.BAD_REQUEST);
    }

    const result = await this.roleRepository.delete(id);
    if (result.affected === 0) {
      throw new HttpException('角色删除失败', HttpStatus.INTERNAL_SERVER_ERROR);
    }

    return { message: '角色删除成功' };
  }

  /**
   * 获取所有角色（用于下拉选择）
   */
  async getAllRoles() {
    return await this.roleRepository.find({
      where: { status: 1 },
      select: ['id', 'code', 'name', 'description'],
      order: { createTime: 'ASC' }
    });
  }

  // ========== 权限管理 ==========

  /**
   * 创建权限
   */
  async createPermission(createPermissionDto: CreatePermissionDto) {
    // 检查权限代码是否已存在
    const existingPermission = await this.permissionRepository.findOne({ 
      where: { code: createPermissionDto.code } 
    });
    if (existingPermission) {
      throw new HttpException('权限代码已存在', HttpStatus.BAD_REQUEST);
    }

    const permission = this.permissionRepository.create({
      ...createPermissionDto,
      status: 1,
      isSystem: 0,
    });

    return await this.permissionRepository.save(permission);
  }

  /**
   * 获取权限列表
   */
  async getPermissions() {
    return await this.permissionRepository.find({
      where: { status: 1 },
      order: { group: 'ASC', createTime: 'ASC' }
    });
  }

  /**
   * 按分组获取权限
   */
  async getPermissionsByGroup() {
    const permissions = await this.permissionRepository.find({
      where: { status: 1 },
      order: { group: 'ASC', createTime: 'ASC' }
    });

    // 按分组整理权限
    const groupedPermissions = permissions.reduce((acc, permission) => {
      if (!acc[permission.group]) {
        acc[permission.group] = [];
      }
      acc[permission.group].push(permission);
      return acc;
    }, {});

    return groupedPermissions;
  }

  /**
   * 更新权限
   */
  async updatePermission(id: string, updatePermissionDto: UpdatePermissionDto) {
    const permission = await this.permissionRepository.findOne({ where: { id } });
    if (!permission) {
      throw new HttpException('权限不存在', HttpStatus.NOT_FOUND);
    }

    Object.assign(permission, updatePermissionDto);
    return await this.permissionRepository.save(permission);
  }

  /**
   * 删除权限
   */
  async deletePermission(id: string) {
    const permission = await this.permissionRepository.findOne({ where: { id } });
    if (!permission) {
      throw new HttpException('权限不存在', HttpStatus.NOT_FOUND);
    }

    if (permission.isSystem === 1) {
      throw new HttpException('系统权限不能删除', HttpStatus.BAD_REQUEST);
    }

    const result = await this.permissionRepository.delete(id);
    if (result.affected === 0) {
      throw new HttpException('权限删除失败', HttpStatus.INTERNAL_SERVER_ERROR);
    }

    return { message: '权限删除成功' };
  }

  // ========== 初始化系统数据 ==========

  /**
   * 初始化系统角色和权限
   */
  async initSystemData() {
    // 创建系统权限
    const systemPermissions = [
      // 用户管理
      { code: 'USER_VIEW', name: '查看用户', group: '用户管理', description: '查看用户列表和详情' },
      { code: 'USER_CREATE', name: '创建用户', group: '用户管理', description: '创建新用户' },
      { code: 'USER_UPDATE', name: '编辑用户', group: '用户管理', description: '编辑用户信息' },
      { code: 'USER_DELETE', name: '删除用户', group: '用户管理', description: '删除用户' },
      
      // 商家管理
      { code: 'MERCHANT_VIEW', name: '查看商家', group: '商家管理', description: '查看商家列表和详情' },
      { code: 'MERCHANT_CREATE', name: '创建商家', group: '商家管理', description: '创建新商家' },
      { code: 'MERCHANT_UPDATE', name: '编辑商家', group: '商家管理', description: '编辑商家信息' },
      { code: 'MERCHANT_DELETE', name: '删除商家', group: '商家管理', description: '删除商家' },
      { code: 'MERCHANT_AUDIT', name: '审核商家', group: '商家管理', description: '审核商家申请' },
      
      // 商品管理
      { code: 'PRODUCT_VIEW', name: '查看商品', group: '商品管理', description: '查看商品列表和详情' },
      { code: 'PRODUCT_CREATE', name: '创建商品', group: '商品管理', description: '创建新商品' },
      { code: 'PRODUCT_UPDATE', name: '编辑商品', group: '商品管理', description: '编辑商品信息' },
      { code: 'PRODUCT_DELETE', name: '删除商品', group: '商品管理', description: '删除商品' },
      
      // 订单管理
      { code: 'ORDER_VIEW', name: '查看订单', group: '订单管理', description: '查看订单列表和详情' },
      { code: 'ORDER_UPDATE', name: '编辑订单', group: '订单管理', description: '编辑订单状态' },
      
      // 财务管理
      { code: 'FINANCE_VIEW', name: '查看财务', group: '财务管理', description: '查看财务数据' },
      { code: 'FINANCE_WITHDRAWAL', name: '提现管理', group: '财务管理', description: '管理商家提现' },
      { code: 'FINANCE_RECHARGE', name: '充值管理', group: '财务管理', description: '管理商家充值' },
      
      // 系统管理
      { code: 'SYSTEM_ROLE', name: '角色管理', group: '系统管理', description: '管理系统角色' },
      { code: 'SYSTEM_PERMISSION', name: '权限管理', group: '系统管理', description: '管理系统权限' },
      { code: 'SYSTEM_ADMIN', name: '管理员管理', group: '系统管理', description: '管理系统管理员' },
      { code: 'SYSTEM_SETTINGS', name: '系统设置', group: '系统管理', description: '系统参数设置' },
      
      // 邀请码管理
      { code: 'INVITE_CODE_VIEW', name: '查看邀请码', group: '邀请码管理', description: '查看邀请码列表' },
      { code: 'INVITE_CODE_CREATE', name: '创建邀请码', group: '邀请码管理', description: '创建新邀请码' },
      { code: 'INVITE_CODE_UPDATE', name: '编辑邀请码', group: '邀请码管理', description: '编辑邀请码信息' },
      { code: 'INVITE_CODE_DELETE', name: '删除邀请码', group: '邀请码管理', description: '删除邀请码' },
    ];

    for (const permData of systemPermissions) {
      const existingPermission = await this.permissionRepository.findOne({ 
        where: { code: permData.code } 
      });
      if (!existingPermission) {
        const permission = this.permissionRepository.create({
          ...permData,
          status: 1,
          isSystem: 1,
        });
        await this.permissionRepository.save(permission);
      }
    }

    // 创建系统角色
    const systemRoles = [
      {
        code: 'SUPER_ADMIN',
        name: '超级管理员',
        description: '拥有所有权限的超级管理员',
        permissions: systemPermissions.map(p => p.code)
      },
      {
        code: 'SALES_MANAGER',
        name: '销售经理',
        description: '负责销售团队管理',
        permissions: ['USER_VIEW', 'MERCHANT_VIEW', 'MERCHANT_AUDIT', 'INVITE_CODE_VIEW', 'INVITE_CODE_CREATE']
      },
      {
        code: 'FINANCE_MANAGER',
        name: '财务经理',
        description: '负责财务管理',
        permissions: ['FINANCE_VIEW', 'FINANCE_WITHDRAWAL', 'FINANCE_RECHARGE', 'ORDER_VIEW']
      },
      {
        code: 'SALES_PERSON',
        name: '业务员',
        description: '负责客户开发',
        permissions: ['MERCHANT_VIEW', 'INVITE_CODE_VIEW', 'INVITE_CODE_CREATE']
      }
    ];

    for (const roleData of systemRoles) {
      const existingRole = await this.roleRepository.findOne({ 
        where: { code: roleData.code } 
      });
      if (!existingRole) {
        const permissions = await this.permissionRepository.findBy({ 
          code: In(roleData.permissions) 
        });
        
        const role = this.roleRepository.create({
          code: roleData.code,
          name: roleData.name,
          description: roleData.description,
          status: 1,
          isSystem: 1,
          permissions: permissions
        });
        
        await this.roleRepository.save(role);
      }
    }

    return { message: '系统数据初始化完成' };
  }
}
