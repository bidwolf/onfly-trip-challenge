import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'
export const travelOrderSchema = toTypedSchema(
  z.object({
    requester_name:
      z.string({ required_error: 'Nome do solicitante é obrigatório', invalid_type_error: 'Nome do solicitante é obrigatório' })
        .min(1, 'Nome do solicitante é obrigatório'),
    destination: z
      .string({ required_error: 'Destino é obrigatório', invalid_type_error: 'Destino é obrigatório' })
      .min(1, 'Destino é obrigatório'),
    departure_date: z
      .string({ required_error: 'Data de ida é obrigatória', invalid_type_error: 'Data de ida é obrigatória' })
      .min(1, 'Data de ida é obrigatória'),
    return_date: z
      .string({ required_error: 'Data de volta é obrigatória', invalid_type_error: 'Data de volta é obrigatória' })
      .min(1, 'Data de volta é obrigatória'),
    price: z
      .number({ required_error: 'Valor total é obrigatório', invalid_type_error: 'Valor total deve ser um número' })
      .min(1, 'Valor total deve ser maior que zero'),
    description: z.string().optional(),
    accommodation: z.string().optional(),
    transportation: z.string().optional(),
  })
)