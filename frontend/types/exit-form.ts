import { Product } from "./product";

export interface ExitItem {
  id: string;
  product: Product;
  quantity: number;
}

export interface ExitForm {
  id: string
  reference: string
  date: string
  destination: string
  reason: string;
  notes: string;
  status: "draft" | "pending" | "completed";
  items: ExitItem[];
  total?: number;
}
