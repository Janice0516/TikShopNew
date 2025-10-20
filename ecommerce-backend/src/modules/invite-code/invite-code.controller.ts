import {
  Controller,
  Post,
  Get,
  Put,
  Delete,
  Body,
  Param,
  Query,
  UseGuards,
  HttpException,
  HttpStatus,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { InviteCodeService } from './invite-code.service';
import { CreateInviteCodeDto, ValidateInviteCodeDto, QueryInviteCodeDto } from './dto/invite-code.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@ApiTags('邀请码管理')
@Controller('invite-code')
export class InviteCodeController {
  constructor(private readonly inviteCodeService: InviteCodeService) {}

  @Post()
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '创建邀请码' })
  async createInviteCode(@Body() createDto: CreateInviteCodeDto) {
    try {
      const result = await this.inviteCodeService.createInviteCode(createDto);
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('创建邀请码错误:', error);
      return {
        code: 400,
        message: error.message,
        data: null,
      };
    }
  }

  @Post('validate')
  @ApiOperation({ summary: '验证邀请码' })
  async validateInviteCode(@Body() validateDto: ValidateInviteCodeDto) {
    try {
      const result = await this.inviteCodeService.validateInviteCode(validateDto);
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('验证邀请码错误:', error);
      return {
        code: 400,
        message: error.message,
        data: null,
      };
    }
  }

  @Get()
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '查询邀请码列表' })
  async queryInviteCodes(@Query() queryDto: QueryInviteCodeDto) {
    try {
      const result = await this.inviteCodeService.queryInviteCodes(queryDto);
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('查询邀请码错误:', error);
      return {
        code: 400,
        message: error.message,
        data: null,
      };
    }
  }

  @Get('stats')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取邀请码统计' })
  async getInviteCodeStats() {
    try {
      const result = await this.inviteCodeService.getInviteCodeStats();
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('获取邀请码统计错误:', error);
      return {
        code: 400,
        message: error.message,
        data: null,
      };
    }
  }

  @Get(':id')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取邀请码详情' })
  async getInviteCodeById(@Param('id') id: string) {
    try {
      const result = await this.inviteCodeService.getInviteCodeById(id);
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('获取邀请码详情错误:', error);
      return {
        code: 400,
        message: error.message,
        data: null,
      };
    }
  }

  @Put(':id/status')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '更新邀请码状态' })
  async updateInviteCodeStatus(
    @Param('id') id: string,
    @Body('status') status: number,
  ) {
    try {
      const result = await this.inviteCodeService.updateInviteCodeStatus(id, status);
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('更新邀请码状态错误:', error);
      return {
        code: 400,
        message: error.message,
        data: null,
      };
    }
  }

  @Delete(':id')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '删除邀请码' })
  async deleteInviteCode(@Param('id') id: string) {
    try {
      const result = await this.inviteCodeService.deleteInviteCode(id);
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('删除邀请码错误:', error);
      return {
        code: 400,
        message: error.message,
        data: null,
      };
    }
  }
}
