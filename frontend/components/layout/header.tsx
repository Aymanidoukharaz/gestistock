"use client"

import { Button } from "@/components/ui/button"
import { useAuth } from "@/hooks/use-auth"
import { Menu, Bell, Search, Sun, Moon } from "lucide-react"
import { usePathname } from "next/navigation"
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet"
import { Input } from "@/components/ui/input"
import { useTheme } from "next-themes"
import { GestistockLogo } from "@/components/gestistock-logo"
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { useEffect, useState } from "react"
import { MobileSidebar } from "./mobile-sidebar"

export function Header() {
  const { user } = useAuth()
  const pathname = usePathname()
  const { theme, setTheme } = useTheme()
  const [mounted, setMounted] = useState(false)

  // Après le montage du composant, on peut accéder au thème
  useEffect(() => {
    setMounted(true)
  }, [])

  // Get page title based on current path
  const getPageTitle = () => {
    const path = pathname.split("/")[1]
    switch (path) {
      case "dashboard":
        return "Tableau de bord"
      case "products":
        return "Gestion des produits"
      case "stocks":
        return "Gestion des stocks"
      case "entries":
        return "Bons d'entrée"
      case "exits":
        return "Bons de sortie"
      case "suppliers":
        return "Fournisseurs"
      case "users":
        return "Utilisateurs"
      case "reports":
        return "Rapports"
      case "categories":
        return "Gestion des catégories"
      default:
        return "GESTISTOCK"
    }
  }

  return (
    <header className="sticky top-0 z-10 flex h-16 items-center border-b bg-white px-4 shadow-sm dark:bg-gray-900 dark:border-gray-800 md:px-6">
      <div className="flex items-center gap-4 md:hidden">
        <Sheet>
          <SheetTrigger asChild>
            <Button variant="outline" size="icon" className="rounded-lg">
              <Menu className="h-5 w-5" />
              <span className="sr-only">Menu</span>
            </Button>
          </SheetTrigger>
          <SheetContent side="left" className="p-0">
            <MobileSidebar />
          </SheetContent>
        </Sheet>
        <div className="flex items-center gap-2">
          <GestistockLogo size={32} />
          <span className="font-bold bg-gradient-to-r from-primary to-purple-700 bg-clip-text text-transparent">
            GESTISTOCK
          </span>
        </div>
      </div>
      <div className="flex flex-1 items-center justify-between">
        <h1 className="text-xl font-semibold md:text-2xl bg-gradient-to-r from-primary to-purple-700 bg-clip-text text-transparent">
          {getPageTitle()}
        </h1>
        <div className="flex items-center gap-4">
          <div className="relative hidden md:block">
            <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500 dark:text-gray-400" />
            <Input
              type="search"
              placeholder="Rechercher..."
              className="w-64 pl-10 rounded-lg border-gray-200 focus-visible:ring-primary dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            />
          </div>

          <Button
            variant="outline"
            size="icon"
            className="rounded-lg border-gray-200 hover:bg-gray-100 hover:text-primary dark:border-gray-700 dark:hover:bg-gray-800"
            onClick={() => setTheme(theme === "dark" ? "light" : "dark")}
          >
            {mounted && theme === "dark" ? <Sun className="h-5 w-5" /> : <Moon className="h-5 w-5" />}
          </Button>

          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button
                variant="outline"
                size="icon"
                className="relative rounded-lg border-gray-200 hover:bg-gray-100 hover:text-primary dark:border-gray-700 dark:hover:bg-gray-800"
              >
                <Bell className="h-5 w-5" />
                <span className="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-primary text-[10px] text-white">
                  3
                </span>
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-80 dark:bg-gray-900 dark:border-gray-800">
              <div className="flex items-center justify-between p-4 border-b dark:border-gray-800">
                <span className="font-medium dark:text-white">Notifications</span>
                <Button variant="ghost" size="sm" className="text-xs text-primary">
                  Marquer comme lues
                </Button>
              </div>
              <div className="max-h-80 overflow-y-auto">
                <div className="p-4 border-b hover:bg-gray-50 cursor-pointer dark:border-gray-800 dark:hover:bg-gray-800">
                  <div className="flex items-start gap-3">
                    <div className="h-2 w-2 mt-1.5 rounded-full bg-primary"></div>
                    <div>
                      <p className="text-sm font-medium dark:text-white">Stock faible</p>
                      <p className="text-xs text-muted-foreground">Le produit "Écran 24 pouces" est en stock faible</p>
                      <p className="text-xs text-muted-foreground mt-1">Il y a 2 heures</p>
                    </div>
                  </div>
                </div>
                <div className="p-4 border-b hover:bg-gray-50 cursor-pointer dark:border-gray-800 dark:hover:bg-gray-800">
                  <div className="flex items-start gap-3">
                    <div className="h-2 w-2 mt-1.5 rounded-full bg-primary"></div>
                    <div>
                      <p className="text-sm font-medium dark:text-white">Nouvelle entrée</p>
                      <p className="text-xs text-muted-foreground">Bon d'entrée BE-2023-0003 créé</p>
                      <p className="text-xs text-muted-foreground mt-1">Il y a 5 heures</p>
                    </div>
                  </div>
                </div>
                <div className="p-4 hover:bg-gray-50 cursor-pointer dark:hover:bg-gray-800">
                  <div className="flex items-start gap-3">
                    <div className="h-2 w-2 mt-1.5 rounded-full bg-primary"></div>
                    <div>
                      <p className="text-sm font-medium dark:text-white">Nouvel utilisateur</p>
                      <p className="text-xs text-muted-foreground">L'utilisateur "Jean Dupont" a été ajouté</p>
                      <p className="text-xs text-muted-foreground mt-1">Il y a 1 jour</p>
                    </div>
                  </div>
                </div>
              </div>
              <div className="p-2 border-t dark:border-gray-800">
                <Button variant="ghost" size="sm" className="w-full text-primary">
                  Voir toutes les notifications
                </Button>
              </div>
            </DropdownMenuContent>
          </DropdownMenu>

          <div className="flex items-center gap-2 md:hidden">
            <div className="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary">
              <span className="text-sm font-medium">{user?.name?.charAt(0) || "U"}</span>
            </div>
          </div>
        </div>
      </div>
    </header>
  )
}
