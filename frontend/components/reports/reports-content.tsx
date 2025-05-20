"use client"

import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { useEffect, useState } from "react"
import { useMockData } from "@/hooks/use-mock-data"
import { FileText, Download, BarChart, PieChart, TrendingUp, Calendar } from "lucide-react"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Input } from "@/components/ui/input"

export function ReportsContent() {
  const { products, stockMovements, entryForms, exitForms } = useMockData()
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

      <Tabs defaultValue="standard" className="space-y-4">
        <TabsList>
          <TabsTrigger value="standard">Rapports standards</TabsTrigger>
          <TabsTrigger value="custom">Rapports personnalisés</TabsTrigger>
        </TabsList>
        <TabsContent value="standard" className="space-y-4">
          <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
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

            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="flex items-center">
                  <PieChart className="mr-2 h-5 w-5 text-primary" />
                  Valorisation du stock
                </CardTitle>
                <CardDescription>Analyse financière des stocks</CardDescription>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Rapport détaillé sur la valeur du stock par catégorie, avec évolution sur les 3 derniers mois.
                </p>
              </CardContent>
              <CardFooter>
                <Button className="w-full" onClick={() => handleGenerateReport("valorisation")}>
                  <Download className="mr-2 h-4 w-4" />
                  Générer (Excel)
                </Button>
              </CardFooter>
            </Card>

            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="flex items-center">
                  <TrendingUp className="mr-2 h-5 w-5 text-primary" />
                  Produits à faible rotation
                </CardTitle>
                <CardDescription>Analyse des produits peu utilisés</CardDescription>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Identifie les produits avec peu ou pas de mouvements sur les 3 derniers mois.
                </p>
              </CardContent>
              <CardFooter>
                <Button className="w-full" onClick={() => handleGenerateReport("faible-rotation")}>
                  <Download className="mr-2 h-4 w-4" />
                  Générer (Excel)
                </Button>
              </CardFooter>
            </Card>

            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="flex items-center">
                  <Calendar className="mr-2 h-5 w-5 text-primary" />
                  Journal des mouvements
                </CardTitle>
                <CardDescription>Historique détaillé des entrées/sorties</CardDescription>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">
                  Journal chronologique de tous les mouvements de stock avec détails des opérateurs et raisons.
                </p>
              </CardContent>
              <CardFooter>
                <Button className="w-full" onClick={() => handleGenerateReport("journal")}>
                  <Download className="mr-2 h-4 w-4" />
                  Générer (PDF)
                </Button>
              </CardFooter>
            </Card>
          </div>
        </TabsContent>
        <TabsContent value="custom">
          <Card>
            <CardHeader>
              <CardTitle>Rapport personnalisé</CardTitle>
              <CardDescription>Configurez les paramètres de votre rapport</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <label className="text-sm font-medium">Type de rapport</label>
                  <Select defaultValue="inventory">
                    <SelectTrigger>
                      <SelectValue placeholder="Sélectionner un type" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="inventory">Inventaire</SelectItem>
                      <SelectItem value="movements">Mouvements</SelectItem>
                      <SelectItem value="valuation">Valorisation</SelectItem>
                      <SelectItem value="alerts">Alertes de stock</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-medium">Format</label>
                  <Select defaultValue="excel">
                    <SelectTrigger>
                      <SelectValue placeholder="Sélectionner un format" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="excel">Excel</SelectItem>
                      <SelectItem value="pdf">PDF</SelectItem>
                      <SelectItem value="csv">CSV</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <label className="text-sm font-medium">Date de début</label>
                  <Input type="date" />
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-medium">Date de fin</label>
                  <Input type="date" />
                </div>
              </div>

              <div className="space-y-2">
                <label className="text-sm font-medium">Catégories</label>
                <Select defaultValue="all">
                  <SelectTrigger>
                    <SelectValue placeholder="Sélectionner des catégories" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">Toutes les catégories</SelectItem>
                    <SelectItem value="electronics">Électronique</SelectItem>
                    <SelectItem value="furniture">Mobilier</SelectItem>
                    <SelectItem value="office">Fournitures de bureau</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div className="space-y-2">
                <label className="text-sm font-medium">Options supplémentaires</label>
                <div className="grid grid-cols-1 gap-2 sm:grid-cols-2">
                  <div className="flex items-center space-x-2">
                    <input type="checkbox" id="include-graphs" className="h-4 w-4 rounded border-gray-300" />
                    <label htmlFor="include-graphs" className="text-sm">
                      Inclure les graphiques
                    </label>
                  </div>
                  <div className="flex items-center space-x-2">
                    <input type="checkbox" id="detailed" className="h-4 w-4 rounded border-gray-300" />
                    <label htmlFor="detailed" className="text-sm">
                      Rapport détaillé
                    </label>
                  </div>
                  <div className="flex items-center space-x-2">
                    <input type="checkbox" id="summary" className="h-4 w-4 rounded border-gray-300" />
                    <label htmlFor="summary" className="text-sm">
                      Inclure résumé
                    </label>
                  </div>
                </div>
              </div>
            </CardContent>
            <CardFooter>
              <Button className="w-full" onClick={() => handleGenerateReport("personnalise")}>
                <Download className="mr-2 h-4 w-4" />
                Générer le rapport
              </Button>
            </CardFooter>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  )
}
