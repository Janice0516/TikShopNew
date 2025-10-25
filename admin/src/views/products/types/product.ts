export interface ProductSize {
  name: string
  stock: number
}

export interface ProductVariant {
  name: string
  image: string
  unit: 'pcs' | 'ml' | 'l' | 'g' | 'kg' | 'bottle' | 'box' | 'piece'
  volume: string
  weight: string
  sizes: ProductSize[]
  type?: 'color' | 'size' | 'volume' | 'weight' | 'style'
}

export interface ProductForm {
  name: string
  categoryId: number | null
  brand: string
  mainImage: string
  images: string
  video: string
  costPrice: number
  suggestPrice: number
  stock: number
  description: string
  status: number
  variants: ProductVariant[]
}

export interface CategoryOption {
  id: number
  name: string
  level?: number
  parentId?: number
  children?: CategoryOption[]
}

export interface UploadResponse {
  url: string
  filename: string
  size: number
}

export interface AdditionalImage {
  name: string
  url: string
  uid: string
}
