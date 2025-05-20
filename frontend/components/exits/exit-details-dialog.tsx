"use client"

import { Button } from "@/components/ui/button"
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from "@/components/ui/dialog"
import type { ExitForm } from "@/types/exit-form"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Badge } from "@/components/ui/badge"
import { Printer } from "lucide-react"

interface ExitDetailsDialogProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  exit: ExitForm | null
}

export function ExitDetailsDialog({ open, onOpenChange, exit }: ExitDetailsDialogProps) {
  if (!exit) return null

  const handlePrint = () => {
    // Mock print functionality
    alert(`Impression du bon de sortie ${exit.reference}`)
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[700px]">
        <DialogHeader>
          <DialogTitle className="flex items-center justify-between">
            <span>Bon de sortie: {exit.reference}</span>
            <Badge
              variant={exit.status === "completed" ? "success" : exit.status === "pending" ? "warning" : "default"}
            >
              {exit.status === "completed" ? "Complété" : exit.status === "pending" ? "En attente" : "Brouillon"}
            </Badge>
          </DialogTitle>
          <DialogDescription>Détails du bon de sortie</DialogDescription>
        </DialogHeader>

        <div className="space-y-6">
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <h3 className="text-sm font-medium text-muted-foreground">Destination</h3>
              <p className="mt-1">{exit.destination}</p>
            </div>
            <div>
              <h3 className="text-sm font-medium text-muted-foreground">Date</h3>
              <p className="mt-1">{new Date(exit.date).toLocaleDateString("fr-FR")}</p>
            </div>
          </div>

          <div>
            <h3 className="text-sm font-medium text-muted-foreground">Raison</h3>
            <p className="mt-1">{exit.reason}</p>
          </div>

          <div>
            <h3 className="mb-2 text-sm font-medium text-muted-foreground">Produits</h3>
            <div className="rounded-md border">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Produit</TableHead>
                    <TableHead className="text-right">Quantité</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {exit.items.map((item) => (
                    <TableRow key={item.id}>
                      <TableCell>{item.productName}</TableCell>
                      <TableCell className="text-right">{item.quantity}</TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </div>
          </div>

          {exit.notes && (
            <div>
              <h3 className="text-sm font-medium text-muted-foreground">Notes</h3>
              <p className="mt-1 whitespace-pre-line">{exit.notes}</p>
            </div>
          )}

          <div className="flex justify-end">
            <Button onClick={handlePrint}>
              <Printer className="mr-2 h-4 w-4" />
              Imprimer
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  )
}
