import { Product } from "./product";

export interface StockMovement {
  id: string;
  product: Product;
  type: "entry" | "exit";
  quantity: number;
  date: string;
  reason: string;
  created_at: string;
  updated_at: string;
}

export interface StockMovementInput {
  productId: string
  type: "entry" | "exit"
  quantity: number
  date: string
  user_id: string
  reason: string
}
