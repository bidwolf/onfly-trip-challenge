import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'

export const loginFormValidationSchema = toTypedSchema(
  z.object({
    email: z.string({
      required_error: 'O email é obrigatório',
      invalid_type_error: 'O email deve ser uma string',
    }).trim().min(1, { message: 'O email é obrigatório' }).max(255, {
      message: 'O email não pode ter mais de 255 caracteres'

    }).email({ message: 'Email inválido' }),
    password: z
      .string()
      .min(9, { message: 'A senha deve ter pelo menos 9 caracteres' })
      .regex(/[0-9]/, { message: 'A senha deve conter pelo menos um número' })
      .regex(/[a-z]/, { message: 'A senha deve conter pelo menos uma letra minúscula' })
      .regex(/[A-Z]/, { message: 'A senha deve conter pelo menos uma letra maiúscula' })
      .regex(/[^a-zA-Z0-9]/, { message: 'A senha deve conter pelo menos um caractere especial' }),
  })
)