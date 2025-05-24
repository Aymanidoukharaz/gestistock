"use client"

import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { Download, Eye, MoreHorizontal, Plus, Printer, Search, Trash2 } from "lucide-react"
import { useEffect, useState } from "react"
import { useApiData } from "@/hooks/use-api-data"
import { Badge } from "@/components/ui/badge"
import { Pagination } from "@/components/ui/pagination"
import { EntryDialog } from "./entry-dialog"
import { EntryDetailsDialog } from "./entry-details-dialog"
import type { EntryForm } from "@/types/entry-form"

export function EntriesContent() {
  const { entryForms, suppliers, deleteEntryForm, isLoadingEntryForms, isLoadingSuppliers } = useApiData()
  const [isLoading, setIsLoading] = useState(true)
  const [searchTerm, setSearchTerm] = useState("")
  const [currentPage, setCurrentPage] = useState(1)
  const [dialogOpen, setDialogOpen] = useState(false)
  const [detailsDialogOpen, setDetailsDialogOpen] = useState(false)
  const [selectedEntry, setSelectedEntry] = useState<EntryForm | null>(null)
  const [filteredEntries, setFilteredEntries] = useState<EntryForm[]>([])

  const itemsPerPage = 10

  useEffect(() => {
    // Simulate loading
    const timer = setTimeout(() => {
      setIsLoading(false)
    }, 1000)

    return () => clearTimeout(timer)
  }, [])

  useEffect(() => {
    let result = [...entryForms]

    // Apply search filter
    if (searchTerm) {
      result = result.filter(
        (entry) =>
          entry.reference.toLowerCase().includes(searchTerm.toLowerCase()) ||
          entry.supplierName.toLowerCase().includes(searchTerm.toLowerCase()),
      )
    }

    // Sort by date (newest first)
    result.sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime())
console.log("[EntriesContent] entryForms length:", entryForms.length);
console.log("[EntriesContent] searchTerm:", searchTerm);
console.log("[EntriesContent] filteredEntries after search/sort:", result.length);
setFilteredEntries(result)
setCurrentPage(1)
}, [entryForms, searchTerm])

const pageCount = Math.ceil(filteredEntries.length / itemsPerPage)
console.log("[EntriesContent] itemsPerPage:", itemsPerPage);
console.log("[EntriesContent] calculated pageCount:", pageCount);
  const paginatedEntries = filteredEntries.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage)

  const handleAddEntry = () => {
    setSelectedEntry(null)
    setDialogOpen(true)
  }

  const handleViewEntry = (entry: EntryForm) => {
    setSelectedEntry(entry)
    setDetailsDialogOpen(true)
  }

  const handleDeleteEntry = (entryId: string) => {
    if (window.confirm("Êtes-vous sûr de vouloir supprimer ce bon d'entrée ?")) {
      deleteEntryForm(entryId)
    }
  }

  const handlePrintEntry = (entry: EntryForm) => {
    // Mock print functionality
    alert(`Impression du bon d'entrée ${entry.reference}`)
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
      <div className="flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
          <h2 className="text-2xl font-bold tracking-tight">Bons d'entrée</h2>
          <p className="text-muted-foreground">Gérez les entrées de stock et les livraisons</p>
        </div>
        <div className="flex flex-col gap-2 sm:flex-row">
          <Button onClick={handleAddEntry}>
            <Plus className="mr-2 h-4 w-4" />
            Nouveau bon d'entrée
          </Button>
        </div>
      </div>

      <Card>
        <CardHeader className="pb-3">
          <CardTitle>Liste des bons d'entrée</CardTitle>
          <CardDescription>{filteredEntries.length} bons d'entrée trouvés</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="flex flex-col gap-4 md:flex-row">
            <div className="relative flex-1">
              <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-gray-500" />
              <Input
                type="search"
                placeholder="Rechercher par référence ou fournisseur..."
                className="pl-8"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
            </div>
          </div>

          <div className="mt-4 rounded-md border">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Référence</TableHead>
                  <TableHead>Date</TableHead>
                  <TableHead>Fournisseur</TableHead>
                  <TableHead>Statut</TableHead>
                  <TableHead className="text-right">Total</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {paginatedEntries.length === 0 ? (
                  <TableRow key="empty-entries-row">
                    <TableCell colSpan={6} className="h-24 text-center">
                      Aucun bon d'entrée trouvé.
                    </TableCell>
                  </TableRow>
                ) : (
                  paginatedEntries.map((entry) => (
                    <TableRow key={entry.id}>
                      <TableCell className="font-medium">{entry.reference}</TableCell>
                      <TableCell>{new Date(entry.date).toLocaleDateString("fr-FR")}</TableCell>
                      <TableCell>{entry.supplierName}</TableCell>
                      <TableCell>
                        <Badge
                          variant={
                            entry.status === "completed"
                              ? "success"
                              : entry.status === "pending"
                                ? "warning"
                                : "default"
                          }
                        >
                          {entry.status === "completed"
                            ? "Complété"
                            : entry.status === "pending"
                              ? "En attente"
                              : "Brouillon"}
                        </Badge>
                      </TableCell>
                      <TableCell className="text-right">
                        {typeof entry.total === 'number'
                          ? entry.total.toLocaleString("fr-FR", {
                              style: "currency",
                              currency: "EUR",
                            })
                          : "0,00 €"}
                      </TableCell>
                      <TableCell className="text-right">
                        <DropdownMenu>
                          <DropdownMenuTrigger asChild>
                            <Button variant="ghost" className="h-8 w-8 p-0">
                              <span className="sr-only">Ouvrir menu</span>
                              <MoreHorizontal className="h-4 w-4" />
                            </Button>
                          </DropdownMenuTrigger>
                          <DropdownMenuContent align="end">
                            <DropdownMenuLabel>Actions</DropdownMenuLabel>
                            <DropdownMenuItem onClick={() => handleViewEntry(entry)}>
                              <Eye className="mr-2 h-4 w-4" />
                              Voir détails
                            </DropdownMenuItem>
                            <DropdownMenuItem onClick={() => handlePrintEntry(entry)}>
                              <Printer className="mr-2 h-4 w-4" />
                              Imprimer
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem onClick={() => handleDeleteEntry(entry.id)} className="text-destructive">
                              <Trash2 className="mr-2 h-4 w-4" />
                              Supprimer
                            </DropdownMenuItem>
                          </DropdownMenuContent>
                        </DropdownMenu>
                      </TableCell>
                    </TableRow>
                  ))
                )}
              </TableBody>
            </Table>
          </div>

          {pageCount > 1 && (
            <div className="mt-4 flex justify-center">
              <Pagination pageCount={pageCount} currentPage={currentPage} onPageChange={setCurrentPage} />
            </div>
          )}
        </CardContent>
      </Card>

      <EntryDialog open={dialogOpen} onOpenChange={setDialogOpen} suppliers={suppliers} />

      <EntryDetailsDialog open={detailsDialogOpen} onOpenChange={setDetailsDialogOpen} entry={selectedEntry} />
    </div>
  )
}
