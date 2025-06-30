import api from '@/services/api'
export type TravelOrderStatus =
  | 'pendente'
  | 'aprovado'
  | 'cancelado'
export interface TravelOrder {
  id: number
  destination: string
  departure_date: string
  return_date: string
  status: TravelOrderStatus
  price: number
  description?: string
  accommodation?: string
  transportation?: string
  created_at: string
  updated_at: string
}

export interface CreateTravelOrderDTO {
  requester_name: string
  destination: string
  departure_date: string
  return_date: string
  price: number
  hosting?: string
  transportation?: string
  description?: string
}

export interface TravelOrdersResponse {
  data: TravelOrder[]
}

export interface TravelOrderResponse {
  data: TravelOrder
}

/**
 * Travel Orders service calls
 */
export const getTravelOrders = async (): Promise<TravelOrdersResponse> => {
  const { data: result } = await api.get<TravelOrdersResponse>('/travel-orders')
  return result
}

export const getTravelOrderById = async (id: number): Promise<TravelOrderResponse> => {
  const { data: result } = await api.get<TravelOrderResponse>(`/travel-orders/${id}`)
  return result
}

export const createTravelOrder = async (
  data: CreateTravelOrderDTO
): Promise<TravelOrderResponse> => {
  const { data: result } = await api.post<TravelOrderResponse>('/travel-orders', data)
  return result
}

export const approveTravelOrder = async (id: number): Promise<TravelOrderResponse> => {
  const { data: result } = await api.patch<TravelOrderResponse>(`/travel-orders/${id}/approve`)
  return result
}

export const cancelTravelOrder = async (id: number): Promise<TravelOrderResponse> => {
  const { data: result } = await api.patch<TravelOrderResponse>(`/travel-orders/${id}/cancel`)
  return result
}

export const deleteTravelOrder = async (id: number): Promise<void> => {
  await api.delete(`/travel-orders/${id}`)
}
