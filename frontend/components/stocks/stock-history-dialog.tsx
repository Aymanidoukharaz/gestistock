"use client"
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from "@/components/ui/dialog"
import type { Product } from "@/types/product"
import { useMockData } from "@/hooks/use-mock-data"
import { useEffect, useState } from "react"
import { ArrowDown, ArrowUp } from "lucide-react"
import type { StockMovement } from "@/types/stock-movement"

interface StockHistoryDialogProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  product: Product | null
}

export function StockHistoryDialog({ open, onOpenChange, product }: StockHistoryDialogProps) {
  const { stockMovements } = useMockData()
  const [productMovements, setProductMovements] = useState<StockMovement[]>([])

  useEffect(() => {
    if (product) {
      const movements = stockMovements
        .filter((movement) => movement.productId === product.id)
        .sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime())
      setProductMovements(movements)
    }
  }, [product, stockMovements])

  if (!product) return null

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle>Historique des mouvements</DialogTitle>
          <DialogDescription>
            {product.name} (Ref: {product.reference})
          </DialogDescription>
        </DialogHeader>
        <div className="max-h-[400px] overflow-y-auto">
          {productMovements.length === 0 ? (
            <div className="py-4 text-center text-muted-foreground">Aucun mouvement enregistré pour ce produit.</div>
          ) : (
            <div className="space-y-4">
              {productMovements.map((movement) => (
                <div key={movement.id} className="flex items-start gap-4 rounded-lg border p-4">
                  <div
                    className={`flex h-10 w-10 items-center justify-center rounded-full ${
                      movement.type === "entry" ? "bg-green-100 text-green-600" : "bg-red-100 text-red-600"
                    }`}
                  >
                    {movement.type === "entry" ? <ArrowDown className="h-5 w-5" /> : <ArrowUp className="h-5 w-5" />}
                  </div>
                  <div className="flex-1">
                    <div className="flex items-center justify-between">
                      <h4 className="font-medium">
                        {movement.type === "entry" ? "Entrée" : "Sortie"} de {movement.quantity} unité(s)
                      </h4>
                      <span className="text-sm text-muted-foreground">
                        {new Date(movement.date).toLocaleString("fr-FR")}
                      </span>
                    </div>
                    <p className="mt-1 text-sm">{movement.reason}</p>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </DialogContent>
    </Dialog>
  )
}
