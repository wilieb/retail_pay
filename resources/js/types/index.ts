export interface User {
  id: number
  name: string
  email: string
  role_id: number
  branch_id?: number
  store_id?: number
  role?: Role
  branch?: Branch
  store?: Store
  created_at?: string
  updated_at?: string
}

export interface Role {
  id: number
  name: string
  permissions?: Permission[]
  created_at?: string
  updated_at?: string
}

export interface Permission {
  id: number
  name: string
  description?: string
  created_at?: string
  updated_at?: string
}

export interface Branch {
  id: number
  name: string
  stores?: Store[]
  users?: User[]
  created_at?: string
  updated_at?: string
}

export interface Store {
  id: number
  name: string
  branch_id: number
  branch?: Branch
  products?: Product[]
  users?: User[]
  created_at?: string
  updated_at?: string
}

export interface Product {
  id: number
  name: string
  sku: string
  quantity: number
  cost_per_unit: number
  retail_price: number
  status: string
  store_id: number
  store?: Store
  created_at?: string
  updated_at?: string
}

export interface Transaction {
  id: number
  product_id: number
  quantity: number
  amount: number
  user_id: number
  from_store_id?: number
  to_store_id?: number
  transaction_id?: string
  transaction_type: 'sale' | 'transfer'
  transaction_date: string
  product?: Product
  user?: User
  fromStore?: Store
  toStore?: Store
  created_at?: string
  updated_at?: string
}

export interface Transfer {
  id: number
  from_store_id: number
  to_store_id: number
  product_id: number
  quantity: number
  status: 'pending' | 'approved' | 'rejected' | 'completed'
  notes?: string
  created_by: number
  created_at?: string
  updated_at?: string
  product?: Product
  fromStore?: Store
  toStore?: Store
  requestedBy?: User
}

export interface Inventory {
  id: number
  product_id: number
  store_id: number
  quantity: number
  last_updated: string
  product?: Product
  store?: Store
}

export interface ApiResponse<T = any> {
  success: boolean
  data?: T
  message?: string
  error?: string
  statusCode?: number
}

export interface PaginatedResponse<T = any> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}
