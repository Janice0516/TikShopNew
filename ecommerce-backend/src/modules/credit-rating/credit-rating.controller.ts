import {
  Controller,
  Get,
  Post,
  Put,
  Delete,
  Body,
  Param,
  Query,
  UseGuards,
  Request,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { CreditRatingService } from './credit-rating.service';
import { CreateCreditRatingDto, UpdateCreditRatingDto, QueryCreditRatingDto } from './dto/credit-rating.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@ApiTags('信用评级管理')
@Controller('credit-rating')
@UseGuards(JwtAuthGuard)
@ApiBearerAuth()
export class CreditRatingController {
  constructor(private readonly creditRatingService: CreditRatingService) {}

  @Post()
  @ApiOperation({ summary: '创建信用评级' })
  async createCreditRating(@Body() createCreditRatingDto: CreateCreditRatingDto, @Request() req) {
    const evaluatorId = req.user.adminId;
    return this.creditRatingService.createCreditRating(createCreditRatingDto, evaluatorId);
  }

  @Get()
  @ApiOperation({ summary: '获取信用评级列表' })
  async getCreditRatingList(@Query() params: QueryCreditRatingDto) {
    return this.creditRatingService.getCreditRatingList(params);
  }

  @Get(':id')
  @ApiOperation({ summary: '获取信用评级详情' })
  async getCreditRatingDetail(@Param('id') id: string) {
    return this.creditRatingService.getCreditRatingDetail(+id);
  }

  @Put(':id')
  @ApiOperation({ summary: '更新信用评级' })
  async updateCreditRating(
    @Param('id') id: string,
    @Body() updateCreditRatingDto: UpdateCreditRatingDto,
  ) {
    return this.creditRatingService.updateCreditRating(+id, updateCreditRatingDto);
  }

  @Delete(':id')
  @ApiOperation({ summary: '删除信用评级' })
  async deleteCreditRating(@Param('id') id: string) {
    return this.creditRatingService.deleteCreditRating(+id);
  }

  @Get('merchant/current')
  @ApiOperation({ summary: '获取当前商户信用评级' })
  async getCurrentMerchantRating(@Request() req: any) {
    return this.creditRatingService.getMerchantCurrentRating(req.user.merchantId);
  }

  @Get('merchant/history')
  @ApiOperation({ summary: '获取当前商户信用评级历史' })
  async getCurrentMerchantRatingHistory(@Request() req: any, @Query() params: any) {
    return this.creditRatingService.getMerchantRatingHistory(req.user.merchantId, params);
  }

  @Get('merchant/:merchantId/current')
  @ApiOperation({ summary: '获取商户当前信用评级' })
  async getMerchantCurrentRating(@Param('merchantId') merchantId: string) {
    return this.creditRatingService.getMerchantCurrentRating(+merchantId);
  }

  @Get('merchant/:merchantId/history')
  @ApiOperation({ summary: '获取商户信用评级历史' })
  async getMerchantRatingHistory(@Param('merchantId') merchantId: string, @Query() params: any) {
    return this.creditRatingService.getMerchantRatingHistory(+merchantId, params);
  }

  @Get('utils/level/:level')
  @ApiOperation({ summary: '根据等级获取分数范围' })
  async getScoreRangeByLevel(@Param('level') level: string) {
    const range = this.creditRatingService.getScoreRangeByLevel(level);
    return {
      code: 200,
      message: '获取成功',
      data: range,
    };
  }

  @Get('utils/score/:score')
  @ApiOperation({ summary: '根据分数获取等级' })
  async getLevelByScore(@Param('score') score: string) {
    const level = this.creditRatingService.calculateLevelByScore(+score);
    return {
      code: 200,
      message: '获取成功',
      data: { level },
    };
  }

  @Post('calculate/:merchantId')
  @ApiOperation({ summary: '自动计算商户信用评级' })
  async calculateMerchantRating(
    @Param('merchantId') merchantId: string,
    @Request() req: any
  ) {
    return this.creditRatingService.calculateMerchantRating(+merchantId, req.user.id);
  }

  @Get('stats')
  @ApiOperation({ summary: '获取信用评级统计信息' })
  async getCreditRatingStats() {
    return {
      code: 200,
      message: '获取信用评级统计成功',
      data: {
        totalRatings: 1,
        activeRatings: 1,
        avgScore: 95.5,
        levelDistribution: {
          AAA: 1,
          AA: 0,
          A: 0,
          BBB: 0,
          BB: 0,
          B: 0,
          C: 0
        }
      }
    };
  }

  @Post('recalculate-all')
  @ApiOperation({ summary: '批量重新计算所有商户信用评级' })
  async recalculateAllMerchantRatings(@Request() req: any) {
    return this.creditRatingService.recalculateAllMerchantRatings(req.user.id);
  }
}
