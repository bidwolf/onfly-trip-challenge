import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'

export const registerFormValidationSchema = toTypedSchema(z.object({
  name: z
    .string()
    .min(2, { message: 'Nome deve ter pelo menos 2 caracteres' })
    .max(100, { message: 'Nome deve ter no máximo 100 caracteres' }),

  email: z
    .string()
    .email({ message: 'Email deve ser válido' }),

  password: z
    .string()
    .min(8, { message: 'Senha deve ter pelo menos 8 caracteres' })
    .regex(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/, {
      message: 'Senha deve conter pelo menos uma letra minúscula, uma maiúscula e um número'
    }),

  password_confirmation: z
    .string()
}).refine((data) => data.password === data.password_confirmation, {
  message: "Senhas não coincidem",
  path: ["password_confirmation"],
})
)
