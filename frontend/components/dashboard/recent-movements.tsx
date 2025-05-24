"use client"

import { useApiData } from "@/hooks/use-api-data"
import { ArrowDown, ArrowUp } from "lucide-react"
import { useEffect, useState } from "react"

interface Movement {
  id: string
  productName: string
  type: "entry" | "exit"
  quantity: number
  date: string
}

export function RecentMovements() {
  const { stockMovements, products, isLoadingStockMovements, isLoadingProducts } = useApiData()
  const [recentMovements, setRecentMovements] = useState<Movement[]>([])

  useEffect(() => {
    // Get product names
    const productMap = products.reduce(
      (acc, product) => {
        acc[product.id] = product.name
        return acc
      },
      {} as Record<string, string>,
    )

    // Get recent movements
    const movements = stockMovements
      .map((movement) => ({
        id: movement.id,
        productName: productMap[movement.productId] || "Produit inconnu",
        type: movement.type,
        quantity: movement.quantity,
        date: movement.date,
      }))
      .sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime())
      .slice(0, 5)

    setRecentMovements(movements)
  }, [stockMovements, products])

  return (
    <div className="space-y-4">
      {recentMovements.map((movement) => (
        <div
          key={movement.id}
          className="flex items-center justify-between space-x-4 rounded-lg p-3 transition-colors hover:bg-muted/50 dark:hover:bg-gray-800/50"
        >
          <div className="flex items-center space-x-4">
            <div
              className={`flex h-10 w-10 items-center justify-center rounded-full ${
                movement.type === "entry"
                  ? "bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400"
                  : "bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400"
              }`}
            >
              {movement.type === "entry" ? <ArrowDown className="h-5 w-5" /> : <ArrowUp className="h-5 w-5" />}
            </div>
            <div>
              <p className="text-sm font-medium dark:text-gray-200">{movement.productName}</p>
              <p className="text-xs text-muted-foreground">
                {new Date(movement.date).toLocaleString("fr-FR", {
                  day: "2-digit",
                  month: "2-digit",
                  hour: "2-digit",
                  minute: "2-digit",
                })}
              </p>
            </div>
          </div>
          <div
            className={`text-sm font-medium ${
              movement.type === "entry" ? "text-green-600 dark:text-green-400" : "text-red-600 dark:text-red-400"
            }`}
          >
            {movement.type === "entry" ? "+" : "-"}
            {movement.quantity}
          </div>
        </div>
      ))}
    </div>
  )
}
