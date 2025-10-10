// 虚拟数据生成器
export const mockData = {
  // 生成虚拟商店数据
  generateStores(count: number = 12) {
    const storeTypes = ['Electronics', 'Fashion', 'Beauty', 'Home', 'Sports', 'Books', 'Toys', 'Food']
    const storeNames = [
      'TechHub Pro', 'Fashion Forward', 'Beauty Paradise', 'Home Sweet Home',
      'Sports Zone', 'Book World', 'Toy Kingdom', 'Foodie Market',
      'Digital Dreams', 'Style Studio', 'Glow Up', 'Cozy Corner'
    ]
    const locations = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia', 'San Antonio', 'San Diego']
    
    const stores = []
    for (let i = 1; i <= count; i++) {
      const followers = Math.floor(Math.random() * 50000) + 1000
      const rating = (Math.random() * 2 + 3).toFixed(1)
      const products = Math.floor(Math.random() * 200) + 10
      
      stores.push({
        id: i,
        name: storeNames[i - 1] || `Store ${i}`,
        type: storeTypes[Math.floor(Math.random() * storeTypes.length)],
        location: locations[Math.floor(Math.random() * locations.length)],
        avatar: `https://picsum.photos/100/100?random=store${i}`,
        cover: `https://picsum.photos/400/200?random=cover${i}`,
        followers: followers,
        rating: parseFloat(rating),
        products: products,
        description: `Welcome to ${storeNames[i - 1] || `Store ${i}`}! We offer the best ${storeTypes[Math.floor(Math.random() * storeTypes.length)].toLowerCase()} products with fast shipping and excellent customer service.`,
        verified: Math.random() > 0.3,
        isLive: Math.random() > 0.7,
        liveViewers: Math.floor(Math.random() * 1000) + 50,
        tags: ['Hot', 'New', 'Sale', 'Trending'].slice(0, Math.floor(Math.random() * 3) + 1)
      })
    }
    return stores
  },

  // 生成虚拟商品数据
  generateProducts(count: number = 20) {
    const brands = ['Apple', 'Samsung', 'Huawei', 'Xiaomi', 'OPPO', 'Vivo', 'OnePlus', 'Realme', 'Nike', 'Adidas', 'Zara', 'H&M']
    const categories = ['Electronics', 'Fashion', 'Beauty', 'Home', 'Sports', 'Books', 'Toys', 'Food']
    const tags = ['Hot', 'New', 'Sale', 'Free Shipping', 'Verified', '7-Day Return']
    const productNames = [
      'iPhone 15 Pro Max', 'Samsung Galaxy S24', 'MacBook Pro M3', 'AirPods Pro 2',
      'Nike Air Max', 'Adidas Ultraboost', 'Zara Summer Dress', 'H&M Casual Shirt',
      'Skincare Set', 'Makeup Kit', 'Home Decor', 'Sports Equipment',
      'Best Seller Book', 'Educational Toy', 'Organic Food', 'Tech Gadget'
    ]
    
    const products = []
    for (let i = 1; i <= count; i++) {
      const price = Math.floor(Math.random() * 5000) + 100
      const originalPrice = price + Math.floor(Math.random() * 1000) + 200
      const sales = Math.floor(Math.random() * 1000) + 10
      const rating = (Math.random() * 2 + 3).toFixed(1)
      const reviews = Math.floor(Math.random() * 500) + 5
      const storeId = Math.floor(Math.random() * 12) + 1
      
      products.push({
        id: i,
        name: productNames[i - 1] || `Product ${i}`,
        price: price,
        originalPrice: originalPrice,
        image: `https://picsum.photos/300/300?random=${i}`,
        description: `This is a high-quality ${productNames[i - 1] || `Product ${i}`} with excellent features and great value for money.`,
        category: categories[Math.floor(Math.random() * categories.length)],
        brand: brands[Math.floor(Math.random() * brands.length)],
        stock: Math.floor(Math.random() * 100) + 10,
        sales: sales,
        rating: parseFloat(rating),
        reviews: reviews,
        tags: tags.slice(0, Math.floor(Math.random() * 3) + 1),
        storeId: storeId,
        storeName: `Store ${storeId}`,
        images: [
          `https://picsum.photos/400/400?random=${i}1`,
          `https://picsum.photos/400/400?random=${i}2`,
          `https://picsum.photos/400/400?random=${i}3`
        ],
        specifications: {
          'Brand': brands[Math.floor(Math.random() * brands.length)],
          'Model': `MODEL-${i.toString().padStart(3, '0')}`,
          'Color': ['Black', 'White', 'Blue', 'Red'][Math.floor(Math.random() * 4)],
          'Size': `${Math.floor(Math.random() * 20) + 10}cm x ${Math.floor(Math.random() * 20) + 10}cm`,
          'Weight': `${Math.floor(Math.random() * 500) + 100}g`
        },
        isLive: Math.random() > 0.6,
        liveViewers: Math.floor(Math.random() * 500) + 10,
        isTrending: Math.random() > 0.7,
        discount: Math.floor(Math.random() * 50) + 10
      })
    }
    return products
  },

  // 生成虚拟分类数据
  generateCategories() {
    return [
      {
        id: 1,
        name: '手机数码',
        icon: '📱',
        children: [
          { id: 11, name: '手机' },
          { id: 12, name: '平板电脑' },
          { id: 13, name: '笔记本电脑' },
          { id: 14, name: '数码配件' }
        ]
      },
      {
        id: 2,
        name: '服装鞋帽',
        icon: '👕',
        children: [
          { id: 21, name: '男装' },
          { id: 22, name: '女装' },
          { id: 23, name: '童装' },
          { id: 24, name: '鞋靴' }
        ]
      },
      {
        id: 3,
        name: '家用电器',
        icon: '🏠',
        children: [
          { id: 31, name: '大家电' },
          { id: 32, name: '小家电' },
          { id: 33, name: '厨房电器' },
          { id: 34, name: '生活电器' }
        ]
      },
      {
        id: 4,
        name: '美妆护肤',
        icon: '💄',
        children: [
          { id: 41, name: '面部护肤' },
          { id: 42, name: '彩妆' },
          { id: 43, name: '香水' },
          { id: 44, name: '个护清洁' }
        ]
      },
      {
        id: 5,
        name: '食品饮料',
        icon: '🍎',
        children: [
          { id: 51, name: '休闲零食' },
          { id: 52, name: '茶酒饮料' },
          { id: 53, name: '生鲜食品' },
          { id: 54, name: '地方特产' }
        ]
      },
      {
        id: 6,
        name: '运动户外',
        icon: '🏃',
        children: [
          { id: 61, name: '运动服装' },
          { id: 62, name: '运动鞋' },
          { id: 63, name: '户外装备' },
          { id: 64, name: '健身器材' }
        ]
      }
    ]
  },

  // 生成虚拟购物车数据
  generateCartItems(products: any[]) {
    const cartItems = []
    const count = Math.floor(Math.random() * 5) + 1
    
    for (let i = 0; i < count; i++) {
      const product = products[Math.floor(Math.random() * products.length)]
      cartItems.push({
        id: i + 1,
        product: product,
        quantity: Math.floor(Math.random() * 3) + 1,
        selected: Math.random() > 0.5
      })
    }
    
    return cartItems
  },

  // 生成虚拟订单数据
  generateOrders(count: number = 10) {
    const statuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled']
    const orders = []
    
    for (let i = 1; i <= count; i++) {
      const status = statuses[Math.floor(Math.random() * statuses.length)]
      const itemCount = Math.floor(Math.random() * 3) + 1
      const items = []
      let totalAmount = 0
      
      for (let j = 0; j < itemCount; j++) {
        const price = Math.floor(Math.random() * 1000) + 100
        const quantity = Math.floor(Math.random() * 2) + 1
        items.push({
          id: j + 1,
          product: {
            id: j + 1,
            name: `商品 ${j + 1}`,
            price: price,
            image: `https://picsum.photos/100/100?random=${j + 1}`
          },
          quantity: quantity,
          price: price
        })
        totalAmount += price * quantity
      }
      
      orders.push({
        id: i,
        orderNo: `ORD${Date.now()}${i.toString().padStart(3, '0')}`,
        status: status,
        totalAmount: totalAmount,
        items: items,
        address: {
          id: 1,
          name: '张三',
          phone: '138****8888',
          province: '广东省',
          city: '深圳市',
          district: '南山区',
          detail: '科技园南区XX大厦XX层',
          isDefault: true
        },
        createTime: new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000).toISOString(),
        payTime: status !== 'pending' ? new Date(Date.now() - Math.random() * 20 * 24 * 60 * 60 * 1000).toISOString() : undefined,
        shipTime: ['shipped', 'delivered'].includes(status) ? new Date(Date.now() - Math.random() * 15 * 24 * 60 * 60 * 1000).toISOString() : undefined,
        deliverTime: status === 'delivered' ? new Date(Date.now() - Math.random() * 10 * 24 * 60 * 60 * 1000).toISOString() : undefined
      })
    }
    
    return orders.sort((a, b) => new Date(b.createTime).getTime() - new Date(a.createTime).getTime())
  },

  // 生成虚拟地址数据
  generateAddresses() {
    return [
      {
        id: 1,
        name: '张三',
        phone: '138****8888',
        province: '广东省',
        city: '深圳市',
        district: '南山区',
        detail: '科技园南区XX大厦XX层',
        isDefault: true
      },
      {
        id: 2,
        name: '李四',
        phone: '139****9999',
        province: '北京市',
        city: '北京市',
        district: '朝阳区',
        detail: '三里屯XX小区XX号楼XX单元',
        isDefault: false
      },
      {
        id: 3,
        name: '王五',
        phone: '137****7777',
        province: '上海市',
        city: '上海市',
        district: '浦东新区',
        detail: '陆家嘴XX路XX号XX室',
        isDefault: false
      }
    ]
  },

  // 生成虚拟用户数据
  generateUser() {
    return {
      id: 1,
      phone: '138****8888',
      nickname: '用户昵称',
      avatar: 'https://picsum.photos/100/100?random=user',
      level: 'VIP',
      points: 1250,
      coupons: 3
    }
  },

  // 生成虚拟轮播图数据
  generateBanners() {
    return [
      {
        id: 1,
        image: 'https://picsum.photos/800/300?random=banner1',
        title: '春季新品上市',
        link: '/products?category=1'
      },
      {
        id: 2,
        image: 'https://picsum.photos/800/300?random=banner2',
        title: '限时特价活动',
        link: '/products?tag=限时特价'
      },
      {
        id: 3,
        image: 'https://picsum.photos/800/300?random=banner3',
        title: '品牌专场',
        link: '/products?brand=Apple'
      },
      {
        id: 4,
        image: 'https://picsum.photos/800/300?random=banner4',
        title: '会员专享优惠',
        link: '/member'
      }
    ]
  }
}
