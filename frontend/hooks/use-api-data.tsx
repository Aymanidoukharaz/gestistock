"use client"

import type React from "react"

import type { Category } from "@/types/category"
import type { Product } from "@/types/product"
import type { StockMovement, StockMovementInput } from "@/types/stock-movement"
import type { Supplier } from "@/types/supplier"
import type { User } from "@/types/user"
import type { EntryForm } from "@/types/entry-form"
import type { ExitForm } from "@/types/exit-form"

export interface PaginationMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
  path?: string;
}
import { createContext, useContext, useEffect, useState } from "react"
import { useAuth } from "./use-auth"
import { productService } from "@/services/product-service"
import { stockMovementService } from "@/services/stock-movement-service"
import { supplierService } from "@/services/supplier-service"
import { userService } from "@/services/user-service"
import { entryFormService } from "@/services/entry-form-service"
import { exitFormService } from "@/services/exit-form-service"
import { toast } from "sonner"

interface ApiDataContextType {
  // Data
  products: Product[]
  categories: Category[]
  stockMovements: StockMovement[]
  stockMovementsMeta: PaginationMeta | null // Added
  suppliers: Supplier[]
  users: User[]
  entryForms: EntryForm[]
  exitForms: ExitForm[]
  
  // Loading states
  isLoadingProducts: boolean
  isLoadingCategories: boolean
  isLoadingStockMovements: boolean
  isLoadingSuppliers: boolean
  isLoadingUsers: boolean
  isLoadingEntryForms: boolean
  isLoadingExitForms: boolean
  
  // Error states
  productsError: Error | null
  categoriesError: Error | null
  stockMovementsError: Error | null
  suppliersError: Error | null
  usersError: Error | null
  entryFormsError: Error | null
  exitFormsError: Error | null
  
  // Operations
  addProduct: (product: Omit<Product, "id">) => Promise<void>
  updateProduct: (product: Product) => Promise<void>
  deleteProduct: (productId: string) => Promise<void>
  
  addStockMovement: (movement: StockMovementInput) => Promise<void>
  
  addSupplier: (supplier: Omit<Supplier, "id">) => Promise<void>
  updateSupplier: (supplier: Supplier) => Promise<void>
  deleteSupplier: (supplierId: string) => Promise<void>
  
  addUser: (user: Omit<User, "id">) => Promise<void>
  updateUser: (user: User) => Promise<void>
  deleteUser: (userId: string) => Promise<void>
  
  addEntryForm: (entryForm: Omit<EntryForm, "id">) => Promise<void>
  deleteEntryForm: (entryFormId: string) => Promise<void>
  
  addExitForm: (exitForm: Omit<ExitForm, "id">) => Promise<void>
  deleteExitForm: (exitFormId: string) => Promise<void>
  
  addCategory: (category: Omit<Category, "id" | "created_at" | "updated_at">) => Promise<void>
  updateCategory: (category: Category) => Promise<void>
  deleteCategory: (categoryId: number) => Promise<void>
  
  // Refresh functions
  refreshProducts: () => Promise<void>
  refreshCategories: () => Promise<void>
  refreshStockMovements: (options?: { page?: number; fetchLastPage?: boolean }) => Promise<void> // Modified
  refreshSuppliers: () => Promise<void>
  refreshUsers: () => Promise<void>
  refreshEntryForms: () => Promise<void>
  refreshExitForms: () => Promise<void>
}

const ApiDataContext = createContext<ApiDataContextType>({
  // Data with empty initial values
  products: [],
  categories: [],
  stockMovements: [],
  stockMovementsMeta: null, // Added
  suppliers: [],
  users: [],
  entryForms: [],
  exitForms: [],
  
  // Loading states
  isLoadingProducts: false,
  isLoadingCategories: false,
  isLoadingStockMovements: false,
  isLoadingSuppliers: false,
  isLoadingUsers: false,
  isLoadingEntryForms: false,
  isLoadingExitForms: false,
  
  // Error states
  productsError: null,
  categoriesError: null,
  stockMovementsError: null,
  suppliersError: null,
  usersError: null,
  entryFormsError: null,
  exitFormsError: null,
  
  // Operations (stub implementations)
  addProduct: async () => {},
  updateProduct: async () => {},
  deleteProduct: async () => {},
  addStockMovement: async () => {},
  addSupplier: async () => {},
  updateSupplier: async () => {},
  deleteSupplier: async () => {},
  addUser: async () => {},
  updateUser: async () => {},
  deleteUser: async () => {},
  addEntryForm: async () => {},
  deleteEntryForm: async () => {},
  addExitForm: async () => {},
  deleteExitForm: async () => {},
  addCategory: async () => {},
  updateCategory: async () => {},
  deleteCategory: async () => {},
  
  // Refresh functions
  refreshProducts: async () => {},
  refreshCategories: async () => {},
  refreshStockMovements: async (_options?: { page?: number; fetchLastPage?: boolean }) => {}, // Modified signature to match type
  refreshSuppliers: async () => {},
  refreshUsers: async () => {},
  refreshEntryForms: async () => {},
  refreshExitForms: async () => {},
})

export function ApiDataProvider({ children }: { children: React.ReactNode }) {
  const { user } = useAuth()
  
  // Data states
  const [products, setProducts] = useState<Product[]>([])
  const [categories, setCategories] = useState<Category[]>([])
  const [stockMovements, setStockMovements] = useState<StockMovement[]>([])
  const [stockMovementsMeta, setStockMovementsMeta] = useState<PaginationMeta | null>(null) // Corrected: Removed duplicate
  const [suppliers, setSuppliers] = useState<Supplier[]>([])
  const [users, setUsers] = useState<User[]>([])
  const [entryForms, setEntryForms] = useState<EntryForm[]>([])
  const [exitForms, setExitForms] = useState<ExitForm[]>([])
  
  // Loading states
  const [isLoadingProducts, setIsLoadingProducts] = useState<boolean>(false)
  const [isLoadingCategories, setIsLoadingCategories] = useState<boolean>(false)
  const [isLoadingStockMovements, setIsLoadingStockMovements] = useState<boolean>(false)
  const [isLoadingSuppliers, setIsLoadingSuppliers] = useState<boolean>(false)
  const [isLoadingUsers, setIsLoadingUsers] = useState<boolean>(false)
  const [isLoadingEntryForms, setIsLoadingEntryForms] = useState<boolean>(false)
  const [isLoadingExitForms, setIsLoadingExitForms] = useState<boolean>(false)
  
  // Error states
  const [productsError, setProductsError] = useState<Error | null>(null)
  const [categoriesError, setCategoriesError] = useState<Error | null>(null)
  const [stockMovementsError, setStockMovementsError] = useState<Error | null>(null)
  const [suppliersError, setSuppliersError] = useState<Error | null>(null) 
  const [usersError, setUsersError] = useState<Error | null>(null)
  const [entryFormsError, setEntryFormsError] = useState<Error | null>(null)
  const [exitFormsError, setExitFormsError] = useState<Error | null>(null)

  // Fetch functions
  const refreshProducts = async () => {
    if (!user) return
    
    setIsLoadingProducts(true)
    setProductsError(null)
    
    try {
      const data = await productService.getAll()
      setProducts(data)
    } catch (error) {
      console.error("Error fetching products:", error)
      setProductsError(error instanceof Error ? error : new Error("Failed to fetch products"))
    } finally {
      setIsLoadingProducts(false)
    }
  }
  
  const refreshCategories = async () => {
    if (!user) return
    
    setIsLoadingCategories(true)
    setCategoriesError(null)
    
    try {
      const data = await productService.getCategories()
      setCategories(data)
    } catch (error) {
      console.error("Error fetching categories:", error)
      setCategoriesError(error instanceof Error ? error : new Error("Failed to fetch categories"))
    } finally {
      setIsLoadingCategories(false)
    }
  }
  
  const refreshStockMovements = async (options?: { page?: number; fetchLastPage?: boolean }) => {
    if (!user) return

    setIsLoadingStockMovements(true)
    setStockMovementsError(null)

    try {
      if (options?.fetchLastPage) {
        // 1. Fetch page 1 to get the latest meta (especially last_page)
        const firstPageResponse = await stockMovementService.getAll({ page: 1 })
        if (!firstPageResponse.meta || typeof firstPageResponse.meta.last_page === 'undefined') {
          console.error("Stock movements API response missing meta data or last_page:", firstPageResponse)
          throw new Error("Invalid response structure from stock movements API (missing meta/last_page)")
        }
        const lastPage = firstPageResponse.meta.last_page

        // 2. Fetch the actual last page
        const lastPageResponse = await stockMovementService.getAll({ page: lastPage })
        if (!lastPageResponse.data || !lastPageResponse.meta) {
            console.error("Stock movements API response missing data or meta for last page:", lastPageResponse)
            throw new Error("Invalid response structure from stock movements API (missing data/meta on last page)")
        }
        setStockMovements([...lastPageResponse.data])
        setStockMovementsMeta(lastPageResponse.meta)
      } else {
        // Default behavior: fetch specified page or page 1
        const pageToFetch = options?.page || 1 // Default to page 1 if no page specified
        const response = await stockMovementService.getAll({ page: pageToFetch })
        if (!response.data || !response.meta) {
            console.error(`Stock movements API response missing data or meta for page ${pageToFetch}:`, response)
            throw new Error(`Invalid response structure from stock movements API (missing data/meta on page ${pageToFetch})`)
        }
        setStockMovements([...response.data])
        setStockMovementsMeta(response.meta)
      }
    } catch (error) {
      console.error("Error fetching stock movements:", error)
      setStockMovementsError(error instanceof Error ? error : new Error("Failed to fetch stock movements"))
      setStockMovements([]) // Clear data on error
      setStockMovementsMeta(null) // Clear meta on error
    } finally {
      setIsLoadingStockMovements(false)
    }
  }
  
  const refreshSuppliers = async () => {
    if (!user) return
    
    setIsLoadingSuppliers(true)
    setSuppliersError(null)
    
    try {
      const data = await supplierService.getAll()
      setSuppliers(data)
    } catch (error) {
      console.error("Error fetching suppliers:", error)
      setSuppliersError(error instanceof Error ? error : new Error("Failed to fetch suppliers"))
    } finally {
      setIsLoadingSuppliers(false)
    }
  }
  
  const refreshUsers = async () => {
    if (!user) return
    
    setIsLoadingUsers(true)
    setUsersError(null)
    
    try {
      const data = await userService.getAll()
      setUsers(data)
    } catch (error) {
      console.error("Error fetching users:", error)
      setUsersError(error instanceof Error ? error : new Error("Failed to fetch users"))
    } finally {
      setIsLoadingUsers(false)
    }
  }
  
  const refreshEntryForms = async () => {
    if (!user) return
    
    setIsLoadingEntryForms(true)
    setEntryFormsError(null)
    
    try {
      const dataFromService = await entryFormService.getAll()
      console.log('[useApiData.refreshEntryForms] Data received from entryFormService.getAll:', dataFromService);
      console.log('[useApiData.refreshEntryForms] Length of data from service:', dataFromService.length);
      setEntryForms(dataFromService)
    } catch (error) {
      console.error("Error fetching entry forms:", error)
      setEntryFormsError(error instanceof Error ? error : new Error("Failed to fetch entry forms"))
    } finally {
      setIsLoadingEntryForms(false)
    }
  }
  
  const refreshExitForms = async () => {
    if (!user) return
    
    setIsLoadingExitForms(true)
    setExitFormsError(null)
    
    try {
      const data = await exitFormService.getAll()
      setExitForms(data)
    } catch (error) {
      console.error("Error fetching exit forms:", error)
      setExitFormsError(error instanceof Error ? error : new Error("Failed to fetch exit forms"))
    } finally {
      setIsLoadingExitForms(false)
    }
  }

  // Initial data load - fetch all data when user is authenticated
  useEffect(() => {
    if (user) {
      refreshProducts()
      refreshCategories()
      refreshStockMovements()
      refreshSuppliers()
      refreshUsers()
      refreshEntryForms()
      refreshExitForms()
    }
  }, [user])

  // Data mutation operations
  const addProduct = async (product: Omit<Product, "id">) => {
    try {
      await productService.create(product)
      toast.success("Produit ajouté avec succès")
      refreshProducts()
      
      // Update categories if this is a new category (based on name)
      // This logic might need refinement if category creation is handled differently
      // For now, we assume categories are fetched and managed by their IDs
      refreshCategories()
    } catch (error) {
      console.error("Error adding product:", error)
      throw error
    }
  }
  
  const updateProduct = async (product: Product) => {
    try {
      await productService.update(product)
      toast.success("Produit mis à jour avec succès")
      refreshProducts()
      
      // Update categories if this contains a new category
      // Update categories if this contains a new category (based on name)
      // This logic might need refinement if category creation is handled differently
      refreshCategories()
    } catch (error) {
      console.error("Error updating product:", error)
      throw error
    }
  }
  
  const deleteProduct = async (productId: string) => {
    try {
      await productService.delete(productId)
      toast.success("Produit supprimé avec succès")
      refreshProducts()
    } catch (error) {
      console.error("Error deleting product:", error)
      throw error
    }
  }
  
  const addStockMovement = async (movement: StockMovementInput) => {
    try {
      await stockMovementService.create(movement)
      toast.success("Mouvement de stock enregistré avec succès")
      await refreshStockMovements({ fetchLastPage: true }) // Modified to fetch last page
      refreshProducts() // Products quantities will change
    } catch (error) {
      console.error("Error adding stock movement:", error)
      // Consider more specific error handling or re-toast
      throw error
    }
  }
  
  const addSupplier = async (supplier: Omit<Supplier, "id">) => {
    try {
      await supplierService.create(supplier)
      toast.success("Fournisseur ajouté avec succès")
      refreshSuppliers()
    } catch (error) {
      console.error("Error adding supplier:", error)
      throw error
    }
  }
  
  const updateSupplier = async (supplier: Supplier) => {
    try {
      await supplierService.update(supplier)
      toast.success("Fournisseur mis à jour avec succès")
      refreshSuppliers()
    } catch (error) {
      console.error("Error updating supplier:", error)
      throw error
    }
  }
  
  const deleteSupplier = async (supplierId: string) => {
    try {
      await supplierService.delete(supplierId)
      toast.success("Fournisseur supprimé avec succès")
      refreshSuppliers()
    } catch (error) {
      console.error("Error deleting supplier:", error)
      throw error
    }
  }
  
  const addUser = async (user: Omit<User, "id">) => {
    try {
      await userService.create(user)
      toast.success("Utilisateur ajouté avec succès")
      refreshUsers()
    } catch (error) {
      console.error("Error adding user:", error)
      throw error
    }
  }
  
  const updateUser = async (user: User) => {
    try {
      await userService.update(user)
      toast.success("Utilisateur mis à jour avec succès")
      refreshUsers()
    } catch (error) {
      console.error("Error updating user:", error)
      throw error
    }
  }
  
  const deleteUser = async (userId: string) => {
    try {
      await userService.delete(userId)
      toast.success("Utilisateur supprimé avec succès")
      refreshUsers()
    } catch (error) {
      console.error("Error deleting user:", error)
      throw error
    }
  }
  
  const addEntryForm = async (entryForm: Omit<EntryForm, "id">) => {
    try {
      await entryFormService.create(entryForm)
      toast.success("Bon d'entrée ajouté avec succès")
      refreshEntryForms()
      refreshProducts() // Products quantities will change
      refreshStockMovements() // New stock movements will be created
    } catch (error) {
      console.error("Error adding entry form:", error)
      throw error
    }
  }
  
  const deleteEntryForm = async (entryFormId: string) => {
    try {
      await entryFormService.delete(entryFormId)
      toast.success("Bon d'entrée supprimé avec succès")
      refreshEntryForms()
      refreshProducts() // Products quantities might change
      refreshStockMovements() // Stock movements might change
    } catch (error) {
      console.error("Error deleting entry form:", error)
      throw error
    }
  }
  
  const addExitForm = async (exitForm: Omit<ExitForm, "id">) => {
    try {
      await exitFormService.create(exitForm)
      toast.success("Bon de sortie ajouté avec succès")
      refreshExitForms()
      refreshProducts() // Products quantities will change
      refreshStockMovements() // New stock movements will be created
    } catch (error) {
      console.error("Error adding exit form:", error)
      throw error
    }
  }
  
  const deleteExitForm = async (exitFormId: string) => {
    try {
      await exitFormService.delete(exitFormId)
      toast.success("Bon de sortie supprimé avec succès")
      refreshExitForms()
      refreshProducts() // Products quantities might change
      refreshStockMovements() // Stock movements might change
    } catch (error) {
      console.error("Error deleting exit form:", error)
      throw error
    }
  }
  
  // Category operations
  const addCategory = async (category: Omit<Category, "id" | "created_at" | "updated_at">) => {
    try {
      await productService.createCategory(category.name, category.description || null);
      toast.success("Catégorie ajoutée avec succès");
      refreshCategories();
    } catch (error) {
      console.error("Error adding category:", error);
      throw error;
    }
  };
  
  const updateCategory = async (category: Category) => {
    try {
      await productService.updateCategory(category.id, category.name, category.description || null);
      toast.success("Catégorie mise à jour avec succès");
      refreshCategories();
      refreshProducts(); // Products with this category will change
    } catch (error) {
      console.error("Error updating category:", error);
      throw error
    }
  }
  
  const deleteCategory = async (categoryId: number) => {
    try {
      await productService.deleteCategory(categoryId);
      toast.success("Catégorie supprimée avec succès");
      refreshCategories();
      refreshProducts(); // Products with this category will change
    } catch (error) {
      console.error("Error deleting category:", error);
      throw error;
    }
  };

  return (
    <ApiDataContext.Provider
      value={{
        // Data
        products,
        categories,
        stockMovements,
        stockMovementsMeta, // Added
        suppliers,
        users,
        entryForms,
        exitForms,
        
        // Loading states
        isLoadingProducts,
        isLoadingCategories,
        isLoadingStockMovements,
        isLoadingSuppliers,
        isLoadingUsers,
        isLoadingEntryForms,
        isLoadingExitForms,
        
        // Error states
        productsError,
        categoriesError,
        stockMovementsError,
        suppliersError,
        usersError,
        entryFormsError,
        exitFormsError,
        
        // Operations
        addProduct,
        updateProduct,
        deleteProduct,
        addStockMovement,
        addSupplier,
        updateSupplier,
        deleteSupplier,
        addUser,
        updateUser,
        deleteUser,
        addEntryForm,
        deleteEntryForm,
        addExitForm,
        deleteExitForm,
        addCategory,
        updateCategory,
        deleteCategory,
        
        // Refresh functions
        refreshProducts,
        refreshCategories,
        refreshStockMovements,
        refreshSuppliers,
        refreshUsers,
        refreshEntryForms,
        refreshExitForms,
      }}
    >
      {children}
    </ApiDataContext.Provider>
  )
}

export const useApiData = () => useContext(ApiDataContext)
