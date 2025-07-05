import api from '@/services/api'
export type TravelOrderStatus =
  | 'pendente'
  | 'aprovado'
  | 'cancelado'
export interface TravelOrder {
  id: string
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

export interface TravelOrderFilters {
  status?: string
  destination?: string
  start_date?: string
  end_date?: string
}

export interface TravelOrderResponse {
  data: TravelOrder
}

/**
 * Travel Orders service calls
 */
export const getTravelOrders = async (filters?: TravelOrderFilters): Promise<TravelOrdersResponse> => {
  const params = new URLSearchParams()

  if (filters?.status) {
    params.append('status', filters.status)
  }
  if (filters?.destination) {
    params.append('destination', filters.destination)
  }
  if (filters?.start_date) {
    params.append('start_date', filters.start_date)
  }
  if (filters?.end_date) {
    params.append('end_date', filters.end_date)
  }

  const queryString = params.toString()
  const url = queryString ? `/travel-orders?${queryString}` : '/travel-orders'

  const { data: result } = await api.get<TravelOrdersResponse>(url)
  return result
}

export const getTravelOrderById = async (id: TravelOrder['id']): Promise<TravelOrderResponse> => {
  const { data: result } = await api.get<TravelOrderResponse>(`/travel-orders/${id}`)
  return result
}

export const createTravelOrder = async (
  data: CreateTravelOrderDTO
): Promise<TravelOrderResponse> => {
  const { data: result } = await api.post<TravelOrderResponse>('/travel-orders', data)
  return result
}

export const approveTravelOrder = async (id: TravelOrder['id']): Promise<TravelOrderResponse> => {
  const { data: result } = await api.patch<TravelOrderResponse>(`/travel-orders/${id}/approve`)
  return result
}

export const cancelTravelOrder = async (id: TravelOrder['id']): Promise<TravelOrderResponse> => {
  const { data: result } = await api.patch<TravelOrderResponse>(`/travel-orders/${id}/cancel`)
  return result
}

export const deleteTravelOrder = async (id: TravelOrder['id']): Promise<void> => {
  await api.delete(`/travel-orders/${id}`)
}
