"use client"

import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { useEffect, useState } from "react"
import { useApiData } from "@/hooks/use-api-data"
import { FileText, Download, BarChart } from "lucide-react"

export function ReportsContent() {
  const { 
    products, 
    stockMovements, 
    entryForms, 
    exitForms,
    isLoadingProducts,
    isLoadingStockMovements,
    isLoadingEntryForms,
    isLoadingExitForms 
  } = useApiData()
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    // Simulate loading
    const timer = setTimeout(() => {
      setIsLoading(false)
    }, 1000)

    return () => clearTimeout(timer)
  }, [])

  const handleGenerateReport = (reportType: string) => {
    // Mock report generation
    alert(`Génération du rapport ${reportType} (simulée)`)
  }

  if (isLoading) {
    return (
      <div className="flex h-full items-center justify-center">
        <div className="h-8 w-8 animate-spin rounded-full border-b-2 border-primary"></div>
      </div>
    )
  }

  return (
    <div className="space-y-4">
      <div>
        <h2 className="text-2xl font-bold tracking-tight">Rapports</h2>
        <p className="text-muted-foreground">Générez et exportez des rapports sur vos stocks et mouvements</p>
      </div>

      <div className="grid gap-4 md:grid-cols-2">
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="flex items-center">
              <FileText className="mr-2 h-5 w-5 text-primary" />
              Inventaire actuel
            </CardTitle>
            <CardDescription>État complet des stocks à date</CardDescription>
          </CardHeader>
          <CardContent>
            <p className="text-sm text-muted-foreground">
              Ce rapport liste tous les produits en stock avec leurs quantités actuelles, valeurs et statuts
              d'alerte.
            </p>
          </CardContent>
          <CardFooter>
            <Button className="w-full" onClick={() => handleGenerateReport("inventaire")}>
              <Download className="mr-2 h-4 w-4" />
              Générer (Excel)
            </Button>
          </CardFooter>
        </Card>

        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="flex items-center">
              <BarChart className="mr-2 h-5 w-5 text-primary" />
              Mouvements mensuels
            </CardTitle>
            <CardDescription>Entrées et sorties du mois</CardDescription>
          </CardHeader>
          <CardContent>
            <p className="text-sm text-muted-foreground">
              Résumé des mouvements de stock du mois en cours avec détails par produit et par jour.
            </p>
          </CardContent>
          <CardFooter>
            <Button className="w-full" onClick={() => handleGenerateReport("mouvements-mensuels")}>
              <Download className="mr-2 h-4 w-4" />
              Générer (PDF)
            </Button>
          </CardFooter>
        </Card>
      </div>
    </div>
  )
}
