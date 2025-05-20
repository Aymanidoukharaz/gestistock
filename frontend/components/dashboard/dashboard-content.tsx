"use client"

import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Activity, AlertTriangle, DollarSign, Package, TrendingUp, TrendingDown } from "lucide-react"
import { ProductCategoryChart } from "./product-category-chart"
import { StockMovementChart } from "./stock-movement-chart"
import { RecentMovements } from "./recent-movements"
import { useEffect, useState } from "react"
import { useMockData } from "@/hooks/use-mock-data"
import { AnalyticsContent } from "./analytics-content"

export function DashboardContent() {
  const { products, stockMovements } = useMockData()
  const [isLoading, setIsLoading] = useState(true)
  const [stats, setStats] = useState({
    totalProducts: 0,
    lowStockAlerts: 0,
    totalValue: 0,
    movementsToday: 0,
    entriesValue: 0,
    exitsValue: 0,
  })

  useEffect(() => {
    // Simulate loading
    const timer = setTimeout(() => {
      // Calculate dashboard stats
      const totalProducts = products.length
      const lowStockAlerts = products.filter((p) => p.quantity < p.minStock).length
      const totalValue = products.reduce((sum, product) => sum + product.price * product.quantity, 0)

      const today = new Date().toISOString().split("T")[0]
      const movementsToday = stockMovements.filter((m) => m.date.startsWith(today)).length

      // Calculate entries and exits values for the last 7 days
      const last7Days = new Date()
      last7Days.setDate(last7Days.getDate() - 7)
      const last7DaysIso = last7Days.toISOString()

      const recentEntries = stockMovements.filter((m) => m.date > last7DaysIso && m.type === "entry")
      const recentExits = stockMovements.filter((m) => m.date > last7DaysIso && m.type === "exit")

      const entriesValue = recentEntries.reduce((sum, movement) => {
        const product = products.find((p) => p.id === movement.productId)
        return sum + (product ? product.price * movement.quantity : 0)
      }, 0)

      const exitsValue = recentExits.reduce((sum, movement) => {
        const product = products.find((p) => p.id === movement.productId)
        return sum + (product ? product.price * movement.quantity : 0)
      }, 0)

      setStats({
        totalProducts,
        lowStockAlerts,
        totalValue,
        movementsToday,
        entriesValue,
        exitsValue,
      })
      setIsLoading(false)
    }, 1000)

    return () => clearTimeout(timer)
  }, [products, stockMovements])

  if (isLoading) {
    return (
      <div className="flex h-full items-center justify-center">
        <div className="h-10 w-10 rounded-full border-4 border-primary border-t-transparent animate-spin"></div>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <Card className="dashboard-card">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Produits</CardTitle>
            <div className="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center">
              <Package className="h-4 w-4 text-primary" />
            </div>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{stats.totalProducts}</div>
            <p className="text-xs text-muted-foreground mt-1">Produits en stock</p>
          </CardContent>
        </Card>

        <Card className="dashboard-card">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Alertes Stock</CardTitle>
            <div className="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center dark:bg-red-900/20">
              <AlertTriangle className="h-4 w-4 text-red-500" />
            </div>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-red-500">{stats.lowStockAlerts}</div>
            <p className="text-xs text-muted-foreground mt-1">Produits sous le seuil minimum</p>
          </CardContent>
        </Card>

        <Card className="dashboard-card">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Valeur Totale</CardTitle>
            <div className="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center dark:bg-green-900/20">
              <DollarSign className="h-4 w-4 text-green-500" />
            </div>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-green-600">
              {stats.totalValue.toLocaleString("fr-FR", {
                style: "currency",
                currency: "EUR",
              })}
            </div>
            <p className="text-xs text-muted-foreground mt-1">Valeur du stock actuel</p>
          </CardContent>
        </Card>

        <Card className="dashboard-card">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Mouvements Aujourd'hui</CardTitle>
            <div className="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center dark:bg-blue-900/20">
              <Activity className="h-4 w-4 text-blue-500" />
            </div>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-blue-600">{stats.movementsToday}</div>
            <p className="text-xs text-muted-foreground mt-1">Entrées et sorties du jour</p>
          </CardContent>
        </Card>
      </div>

      <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-7">
        <Card className="dashboard-card lg:col-span-3">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="flex items-center gap-2">
              <TrendingUp className="h-5 w-5 text-green-500" />
              <span>Entrées (7 derniers jours)</span>
            </CardTitle>
            <div className="text-xl font-bold text-green-600">
              {stats.entriesValue.toLocaleString("fr-FR", {
                style: "currency",
                currency: "EUR",
              })}
            </div>
          </CardHeader>
        </Card>

        <Card className="dashboard-card lg:col-span-3">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="flex items-center gap-2">
              <TrendingDown className="h-5 w-5 text-red-500" />
              <span>Sorties (7 derniers jours)</span>
            </CardTitle>
            <div className="text-xl font-bold text-red-500">
              {stats.exitsValue.toLocaleString("fr-FR", {
                style: "currency",
                currency: "EUR",
              })}
            </div>
          </CardHeader>
        </Card>
      </div>

      <Tabs defaultValue="overview" className="space-y-6">
        <TabsList className="bg-muted/50 p-1 dark:bg-gray-800">
          <TabsTrigger
            value="overview"
            className="rounded-md data-[state=active]:bg-white data-[state=active]:shadow-sm dark:data-[state=active]:bg-gray-900"
          >
            Vue d'ensemble
          </TabsTrigger>
          <TabsTrigger
            value="analytics"
            className="rounded-md data-[state=active]:bg-white data-[state=active]:shadow-sm dark:data-[state=active]:bg-gray-900"
          >
            Analytique
          </TabsTrigger>
        </TabsList>

        <TabsContent value="overview" className="space-y-6">
          <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-7">
            <Card className="dashboard-card lg:col-span-4">
              <CardHeader>
                <CardTitle className="text-lg">Répartition par Catégorie</CardTitle>
              </CardHeader>
              <CardContent className="pl-2">
                <ProductCategoryChart />
              </CardContent>
            </Card>

            <Card className="dashboard-card lg:col-span-3">
              <CardHeader>
                <CardTitle className="text-lg">Mouvements Récents</CardTitle>
              </CardHeader>
              <CardContent>
                <RecentMovements />
              </CardContent>
            </Card>
          </div>

          <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-7">
            <Card className="dashboard-card lg:col-span-7">
              <CardHeader>
                <CardTitle className="text-lg">Mouvements de Stock</CardTitle>
              </CardHeader>
              <CardContent className="pl-2">
                <StockMovementChart />
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        <TabsContent value="analytics" className="space-y-6">
          <AnalyticsContent />
        </TabsContent>
      </Tabs>
    </div>
  )
}
