import api from '@/services/api'

export interface RegisterDTO {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export interface LoginDTO {
  email: string
  password: string
}
export interface ErrorResponse<T = Record<string, string[]>> {
  message: string
  errors: T
}
export interface AuthTokens {
  token: string
  type: 'Bearer'
  expires_in: number
}

export interface RegisterResponse extends AuthTokens {
  message: string
}

export interface LoginResponse extends AuthTokens { }

export interface User {
  id: number
  name: string
  email: string
  is_admin: boolean
  created_at: string
  updated_at: string
}

export interface LogoutResponse {
  message: string
}

/**
 * Auth service calls
 */
export const register = async (
  data: RegisterDTO
): Promise<RegisterResponse> => {
  const { data: result } = await api.post<RegisterResponse>('/auth/register', data)
  return result
}

export const login = async (
  data: LoginDTO
): Promise<LoginResponse> => {
  const { data: result } = await api.post<LoginResponse>('/auth/login', data)
  return result
}

export const me = async (): Promise<User> => {
  const { data: result } = await api.get<User>('/me')
  return result
}

export const logout = async (): Promise<LogoutResponse> => {
  const { data: result } = await api.post<LogoutResponse>('/auth/logout')
  return result
}

export const refresh = async (): Promise<LoginResponse> => {
  const { data: result } = await api.post<LoginResponse>('/auth/refresh')
  return result
}