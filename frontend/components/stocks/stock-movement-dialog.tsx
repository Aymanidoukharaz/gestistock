"use client"

import { Button } from "@/components/ui/button"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form"
import { Input } from "@/components/ui/input"
import { Textarea } from "@/components/ui/textarea"
import type { Product } from "@/types/product"
import { zodResolver } from "@hookform/resolvers/zod"
import { useForm } from "react-hook-form"
import { z } from "zod"
import { useEffect } from "react"
import { ArrowDown, ArrowUp } from "lucide-react"

const movementSchema = z.object({
  quantity: z.coerce.number().positive("La quantité doit être positive").int("La quantité doit être un nombre entier"),
  reason: z.string().min(1, "La raison est requise"),
})

interface StockMovementDialogProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  product: Product | null
  type: "entry" | "exit"
  onSave: (productId: string, quantity: number, type: "entry" | "exit", reason: string) => void
}

export function StockMovementDialog({ open, onOpenChange, product, type, onSave }: StockMovementDialogProps) {
  const form = useForm<z.infer<typeof movementSchema>>({
    resolver: zodResolver(movementSchema),
    defaultValues: {
      quantity: 1,
      reason: "",
    },
  })

  useEffect(() => {
    if (open) {
      form.reset({
        quantity: 1,
        reason: "",
      })
    }
  }, [open, form])

  function onSubmit(values: z.infer<typeof movementSchema>) {
    if (product) {
      onSave(product.id, values.quantity, type, values.reason)
    }
  }

  if (!product) return null

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[500px]">
        <DialogHeader>
          <DialogTitle>
            {type === "entry" ? (
              <div className="flex items-center">
                <ArrowDown className="mr-2 h-5 w-5 text-green-500" />
                Entrée de stock
              </div>
            ) : (
              <div className="flex items-center">
                <ArrowUp className="mr-2 h-5 w-5 text-red-500" />
                Sortie de stock
              </div>
            )}
          </DialogTitle>
          <DialogDescription>
            {product.name} (Ref: {product.reference})
            <div className="mt-1">
              Stock actuel: <strong>{product.quantity}</strong>
            </div>
          </DialogDescription>
        </DialogHeader>
        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
            <FormField
              control={form.control}
              name="quantity"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Quantité</FormLabel>
                  <FormControl>
                    <Input type="number" min="1" max={type === "exit" ? product.quantity : undefined} {...field} />
                  </FormControl>
                  {type === "exit" && product.quantity < field.value && (
                    <p className="text-sm text-destructive">La quantité ne peut pas dépasser le stock disponible.</p>
                  )}
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={form.control}
              name="reason"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Raison</FormLabel>
                  <FormControl>
                    <Textarea
                      placeholder={
                        type === "entry"
                          ? "Ex: Réapprovisionnement, retour client..."
                          : "Ex: Vente, perte, détérioration..."
                      }
                      {...field}
                    />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <DialogFooter>
              <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
                Annuler
              </Button>
              <Button type="submit" disabled={type === "exit" && form.getValues().quantity > (product?.quantity || 0)}>
                Confirmer
              </Button>
            </DialogFooter>
          </form>
        </Form>
      </DialogContent>
    </Dialog>
  )
}
