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
import { zodResolver } from "@hookform/resolvers/zod"
import { useForm } from "react-hook-form"
import { z } from "zod"
import { useEffect, useState } from "react"
import { useApiData } from "@/hooks/use-api-data"

const categorySchema = z.object({
  name: z.string().min(1, "Le nom de la catégorie est requis"),
})

interface CategoryDialogProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  category: string | null
  onSave: (oldCategory: string | null, newCategory: string) => void
}

export function CategoryDialog({ open, onOpenChange, category, onSave }: CategoryDialogProps) {
  const { categories, isLoadingCategories } = useApiData()
  const [isSubmitting, setIsSubmitting] = useState(false)

  const form = useForm<z.infer<typeof categorySchema>>({
    resolver: zodResolver(categorySchema),
    defaultValues: {
      name: "",
    },
  })

  useEffect(() => {
    if (category) {
      form.reset({
        name: category,
      })
    } else {
      form.reset({
        name: "",
      })
    }
  }, [category, form, open])

  const onSubmit = async (values: z.infer<typeof categorySchema>) => {
    setIsSubmitting(true)

    try {
      // Check if category already exists (only for new categories)
      if (!category && categories.includes(values.name)) {
        form.setError("name", {
          type: "manual",
          message: "Cette catégorie existe déjà",
        })
        setIsSubmitting(false)
        return
      }

      // Simulate API delay
      await new Promise((resolve) => setTimeout(resolve, 500))

      onSave(category, values.name)
      onOpenChange(false)
    } catch (error) {
      console.error("Error submitting category:", error)
    } finally {
      setIsSubmitting(false)
    }
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>{category ? "Modifier la catégorie" : "Ajouter une catégorie"}</DialogTitle>
          <DialogDescription>
            {category ? "Modifiez le nom de la catégorie ci-dessous." : "Entrez le nom de la nouvelle catégorie."}
          </DialogDescription>
        </DialogHeader>
        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
            <FormField
              control={form.control}
              name="name"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Nom de la catégorie</FormLabel>
                  <FormControl>
                    <Input placeholder="Nom de la catégorie" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <DialogFooter>
              <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
                Annuler
              </Button>
              <Button type="submit" disabled={isSubmitting}>
                {isSubmitting ? "Enregistrement..." : category ? "Mettre à jour" : "Créer"}
              </Button>
            </DialogFooter>
          </form>
        </Form>
      </DialogContent>
    </Dialog>
  )
}
