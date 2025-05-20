"use client"

import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { useMockData } from "@/hooks/use-mock-data"
import { useEffect, useState } from "react"
import { Chart } from "@/components/ui/chart"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { BarChart4, PieChart, TrendingUp, Calendar } from "lucide-react"

export function AnalyticsContent() {
  const { products, stockMovements } = useMockData()
  const [timeRange, setTimeRange] = useState("month")
  const [productTurnoverData, setProductTurnoverData] = useState<any[]>([])
  const [categoryValueData, setCategoryValueData] = useState<any[]>([])
  const [stockEvolutionData, setStockEvolutionData] = useState<any[]>([])
  const [topProductsData, setTopProductsData] = useState<any[]>([])

  useEffect(() => {
    // Simuler des données pour le taux de rotation des produits
    const turnoverData = [
      { name: "Électronique", value: 4.2 },
      { name: "Mobilier", value: 1.8 },
      { name: "Fournitures", value: 6.5 },
    ]
    setProductTurnoverData(turnoverData)

    // Simuler des données pour la valeur par catégorie
    const categoryData = [
      { name: "Électronique", value: 12500, fill: "#8b5cf6" }, // violet
      { name: "Mobilier", value: 8700, fill: "#ec4899" }, // rose
      { name: "Fournitures", value: 3200, fill: "#3b82f6" }, // bleu
    ]
    setCategoryValueData(categoryData)

    // Simuler des données pour l'évolution des stocks
    const stockData = [
      { date: "Jan", stock: 15000 },
      { date: "Fév", stock: 16200 },
      { date: "Mar", stock: 14800 },
      { date: "Avr", stock: 17500 },
      { date: "Mai", stock: 18200 },
      { date: "Juin", stock: 19100 },
      { date: "Juil", stock: 20500 },
      { date: "Août", stock: 22000 },
      { date: "Sep", stock: 21500 },
      { date: "Oct", stock: 23000 },
      { date: "Nov", stock: 24500 },
      { date: "Déc", stock: 24000 },
    ]
    setStockEvolutionData(stockData)

    // Simuler des données pour les produits les plus vendus
    const topProducts = [
      { name: "Écran 24 pouces", sales: 42 },
      { name: "Clavier sans fil", sales: 38 },
      { name: "Souris optique", sales: 35 },
      { name: "Papier A4", sales: 30 },
      { name: "Stylos bleus", sales: 25 },
    ]
    setTopProductsData(topProducts)
  }, [products, stockMovements, timeRange])

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h2 className="text-xl font-semibold">Analytique Avancée</h2>
        <Select value={timeRange} onValueChange={setTimeRange}>
          <SelectTrigger className="w-[180px]">
            <SelectValue placeholder="Période" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="week">7 derniers jours</SelectItem>
            <SelectItem value="month">30 derniers jours</SelectItem>
            <SelectItem value="quarter">3 derniers mois</SelectItem>
            <SelectItem value="year">Année en cours</SelectItem>
          </SelectContent>
        </Select>
      </div>

      <div className="grid gap-6 md:grid-cols-2">
        <Card className="dashboard-card">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-lg flex items-center gap-2">
              <BarChart4 className="h-5 w-5 text-primary" />
              Taux de rotation des produits
            </CardTitle>
          </CardHeader>
          <CardContent className="h-[300px]">
            <Chart
              type="bar"
              data={productTurnoverData}
              index="name"
              categories={["value"]}
              colors={["primary"]}
              valueFormatter={(value) => `${value.toFixed(1)} rotations/an`}
              className="h-full"
            />
          </CardContent>
        </Card>

        <Card className="dashboard-card">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-lg flex items-center gap-2">
              <PieChart className="h-5 w-5 text-primary" />
              Valeur du stock par catégorie
            </CardTitle>
          </CardHeader>
          <CardContent className="h-[300px]">
            <Chart
              type="pie"
              data={categoryValueData}
              index="name"
              categories={["value"]}
              colors={categoryValueData.map((item) => item.fill)}
              valueFormatter={(value) => `${value.toLocaleString("fr-FR")} €`}
              className="h-full"
              useCustomColors={true}
            />
          </CardContent>
        </Card>

        <Card className="dashboard-card">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-lg flex items-center gap-2">
              <TrendingUp className="h-5 w-5 text-primary" />
              Évolution de la valeur du stock
            </CardTitle>
          </CardHeader>
          <CardContent className="h-[300px]">
            <Chart
              type="line"
              data={stockEvolutionData}
              index="date"
              categories={["stock"]}
              colors={["primary"]}
              valueFormatter={(value) => `${value.toLocaleString("fr-FR")} €`}
              className="h-full"
            />
          </CardContent>
        </Card>

        <Card className="dashboard-card">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-lg flex items-center gap-2">
              <Calendar className="h-5 w-5 text-primary" />
              Produits les plus vendus
            </CardTitle>
          </CardHeader>
          <CardContent className="h-[300px]">
            <Chart
              type="bar"
              data={topProductsData}
              index="name"
              categories={["sales"]}
              colors={["#8b5cf6"]}
              valueFormatter={(value) => `${value} unités`}
              className="h-full"
            />
          </CardContent>
        </Card>
      </div>

      <Card className="dashboard-card">
        <CardHeader>
          <CardTitle className="text-lg">Indicateurs de performance</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid gap-6 md:grid-cols-3">
            <div className="flex flex-col space-y-2">
              <span className="text-sm text-muted-foreground">Taux de rupture de stock</span>
              <div className="flex items-end gap-2">
                <span className="text-2xl font-bold">2.4%</span>
                <span className="text-xs text-green-500">-0.8%</span>
              </div>
              <div className="h-2 w-full bg-muted rounded-full overflow-hidden">
                <div className="h-full bg-green-500 rounded-full" style={{ width: "2.4%" }}></div>
              </div>
            </div>

            <div className="flex flex-col space-y-2">
              <span className="text-sm text-muted-foreground">Précision des prévisions</span>
              <div className="flex items-end gap-2">
                <span className="text-2xl font-bold">92.7%</span>
                <span className="text-xs text-green-500">+1.2%</span>
              </div>
              <div className="h-2 w-full bg-muted rounded-full overflow-hidden">
                <div className="h-full bg-primary rounded-full" style={{ width: "92.7%" }}></div>
              </div>
            </div>

            <div className="flex flex-col space-y-2">
              <span className="text-sm text-muted-foreground">Taux d'utilisation de l'espace</span>
              <div className="flex items-end gap-2">
                <span className="text-2xl font-bold">78.3%</span>
                <span className="text-xs text-yellow-500">+0.3%</span>
              </div>
              <div className="h-2 w-full bg-muted rounded-full overflow-hidden">
                <div className="h-full bg-yellow-500 rounded-full" style={{ width: "78.3%" }}></div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
