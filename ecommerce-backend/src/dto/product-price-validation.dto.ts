import { IsNumber, IsPositive, Validate, ValidationArguments, ValidatorConstraint, ValidatorConstraintInterface, IsOptional } from 'class-validator';

@ValidatorConstraint({ name: 'costPriceLessThanSuggestPrice', async: false })
export class CostPriceLessThanSuggestPriceConstraint implements ValidatorConstraintInterface {
  validate(costPrice: number, args: ValidationArguments) {
    const object = args.object as any;
    return costPrice < object.suggestPrice;
  }

  defaultMessage(args: ValidationArguments) {
    return '成本价必须低于建议价';
  }
}

@ValidatorConstraint({ name: 'reasonableProfitMargin', async: false })
export class ReasonableProfitMarginConstraint implements ValidatorConstraintInterface {
  validate(suggestPrice: number, args: ValidationArguments) {
    const object = args.object as any;
    const costPrice = object.costPrice;
    if (!costPrice || costPrice <= 0) return true;
    
    const profitMargin = ((suggestPrice - costPrice) / costPrice) * 100;
    return profitMargin >= 5 && profitMargin <= 500;
  }

  defaultMessage(args: ValidationArguments) {
    return '利润率必须在5%-500%之间';
  }
}

export class CreateProductDto {
  @IsNumber({}, { message: '成本价必须是数字' })
  @IsPositive({ message: '成本价必须大于0' })
  @Validate(CostPriceLessThanSuggestPriceConstraint, {
    message: '成本价必须低于建议价'
  })
  costPrice: number;

  @IsNumber({}, { message: '建议价必须是数字' })
  @IsPositive({ message: '建议价必须大于0' })
  @Validate(ReasonableProfitMarginConstraint, {
    message: '利润率必须在5%-500%之间'
  })
  suggestPrice: number;

  @IsOptional()
  @IsNumber({}, { message: '售价必须是数字' })
  @IsPositive({ message: '售价必须大于0' })
  salePrice?: number;
}

export class UpdateProductDto {
  @IsOptional()
  @IsNumber({}, { message: '成本价必须是数字' })
  @IsPositive({ message: '成本价必须大于0' })
  costPrice?: number;

  @IsOptional()
  @IsNumber({}, { message: '建议价必须是数字' })
  @IsPositive({ message: '建议价必须大于0' })
  suggestPrice?: number;

  @IsOptional()
  @IsNumber({}, { message: '售价必须是数字' })
  @IsPositive({ message: '售价必须大于0' })
  salePrice?: number;
}
