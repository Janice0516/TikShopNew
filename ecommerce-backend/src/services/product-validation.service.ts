import { Injectable } from '@nestjs/common';

export interface PriceValidationResult {
  isValid: boolean;
  errors: string[];
  warnings: string[];
  profitMargin?: number;
  suggestions?: string[];
}

@Injectable()
export class ProductValidationService {
  validateProductPrices(costPrice: number, suggestPrice: number, salePrice?: number): PriceValidationResult {
    const errors: string[] = [];
    const warnings: string[] = [];
    const suggestions: string[] = [];
    
    // 基础验证
    if (costPrice <= 0) {
      errors.push('成本价必须大于0');
    }
    
    if (suggestPrice <= 0) {
      errors.push('建议价必须大于0');
    }
    
    if (costPrice >= suggestPrice) {
      errors.push('成本价必须低于建议价');
    }
    
    // 利润率验证
    if (costPrice > 0 && suggestPrice > costPrice) {
      const profitMargin = ((suggestPrice - costPrice) / costPrice) * 100;
      
      if (profitMargin < 5) {
        warnings.push(`利润率过低（${profitMargin.toFixed(1)}%），建议至少5%`);
        suggestions.push(`建议价至少设为 RM${(costPrice * 1.05).toFixed(2)}`);
      }
      
      if (profitMargin > 500) {
        warnings.push(`利润率过高（${profitMargin.toFixed(1)}%），建议不超过500%`);
        suggestions.push(`建议价不超过 RM${(costPrice * 6).toFixed(2)}`);
      }
      
      // 售价验证（如果提供）
      if (salePrice) {
        if (salePrice < costPrice) {
          errors.push('售价不能低于成本价');
        }
        
        if (salePrice > suggestPrice * 1.5) {
          warnings.push('售价不应超过建议价的150%');
        }
        
        const saleProfitMargin = ((salePrice - costPrice) / costPrice) * 100;
        if (saleProfitMargin < 10) {
          warnings.push(`售价利润率较低（${saleProfitMargin.toFixed(1)}%）`);
        }
      }
      
      return {
        isValid: errors.length === 0,
        errors,
        warnings,
        suggestions,
        profitMargin: parseFloat(profitMargin.toFixed(2))
      };
    }
    
    return {
      isValid: errors.length === 0,
      errors,
      warnings,
      suggestions
    };
  }
  
  validateMerchantProductPrices(costPrice: number, salePrice: number): PriceValidationResult {
    const errors: string[] = [];
    const warnings: string[] = [];
    const suggestions: string[] = [];
    
    if (costPrice <= 0) {
      errors.push('成本价必须大于0');
    }
    
    if (salePrice <= 0) {
      errors.push('售价必须大于0');
    }
    
    if (costPrice > 0 && salePrice > 0) {
      if (salePrice < costPrice) {
        errors.push('售价不能低于成本价');
      } else {
        const profitMargin = ((salePrice - costPrice) / costPrice) * 100;
        
        if (profitMargin < 10) {
          warnings.push(`利润率较低（${profitMargin.toFixed(1)}%）`);
          suggestions.push(`建议售价至少 RM${(costPrice * 1.1).toFixed(2)}`);
        }
        
        if (profitMargin > 300) {
          warnings.push(`利润率较高（${profitMargin.toFixed(1)}%）`);
        }
      }
    }
    
    return {
      isValid: errors.length === 0,
      errors,
      warnings,
      suggestions
    };
  }
  
  getPriceSuggestions(costPrice: number): { minSuggest: number; maxSuggest: number; recommendedSuggest: number } {
    const minSuggest = costPrice * 1.05; // 5% 利润率
    const maxSuggest = costPrice * 6; // 500% 利润率
    const recommendedSuggest = costPrice * 1.3; // 30% 利润率
    
    return {
      minSuggest: parseFloat(minSuggest.toFixed(2)),
      maxSuggest: parseFloat(maxSuggest.toFixed(2)),
      recommendedSuggest: parseFloat(recommendedSuggest.toFixed(2))
    };
  }
}
