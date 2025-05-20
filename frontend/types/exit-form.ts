export interface ExitItem {
  id: string
  productId: string
  productName: string
  quantity: number
}

export interface ExitForm {
  id: string
  reference: string
  date: string
  destination: string
  reason: string
  notes: string
  status: "draft" | "pending" | "completed"
  items: ExitItem[]
}
