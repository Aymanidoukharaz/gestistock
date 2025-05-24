export interface StockMovement {
  id: string
  productId: string
  type: "entry" | "exit"
  quantity: number
  date: string
  reason: string
}

export interface StockMovementInput {
  productId: string
  type: "entry" | "exit"
  quantity: number
  date: string
  user_id: string
  reason: string
}
