import api from "@/lib/api";
import { Product } from "@/types/product";
import { Category } from "@/types/category";

const parseApiResponse = <T>(data: any): T => {
  if (typeof data === 'string' && data.startsWith('+')) {
    try {
      return JSON.parse(data.substring(1)) as T;
    } catch (error) {
      throw new Error("Invalid JSON response from API after removing '+': " + (error as Error).message);
    }
  }
  return data as T;
};

const transformProduct = (product: any): Product => {
  return {
    ...product,
    id: String(product.id),
    price: Number(product.price),
    quantity: Number(product.quantity),
    min_stock: Number(product.min_stock),
    category: product.category,
  };
};

export const productService = {
  getAll: async (): Promise<Product[]> => {
    const response = await api.get<any>("/products");
    
    const parsedData = parseApiResponse<any>(response.data);
    
    const products = parsedData?.data || parsedData;
    
    const transformedProducts = Array.isArray(products) ? products.map(transformProduct) : [];
    
    return transformedProducts;
  },
    getById: async (id: string): Promise<Product> => {
    const response = await api.get<any>(`/products/${id}`);
    const productData = parseApiResponse<any>(response.data);
    return transformProduct(productData);
  },
  
  create: async (product: Omit<Product, "id">): Promise<Product> => {
    const dataToSend = {
      ...product,
      category_id: product.category?.id,
    };
    delete (dataToSend as Partial<Product>).category;

    const response = await api.post<Product>("/products", dataToSend);
    return parseApiResponse<Product>(response.data);
  },
  
  update: async (product: Product): Promise<Product> => {
    const dataToSend = {
      ...product,
      category_id: product.category?.id,
    };
    delete (dataToSend as any).category;
    
    const response = await api.put<Product>(`/products/${product.id}`, dataToSend);
    return parseApiResponse<Product>(response.data);
  },
  
  delete: async (id: string): Promise<void> => {
    await api.delete(`/products/${id}`);
  },
  getCategories: async (): Promise<Category[]> => {
    try {
      const response = await api.get<any>("/products/categories");

      let parsedData: any;
      if (typeof response.data === 'string') {
        const cleanData = response.data.startsWith('+') ? response.data.substring(1) : response.data;
        parsedData = JSON.parse(cleanData);
      } else {
        parsedData = response.data;
      }

      if (parsedData.success && parsedData.data) {
        return parsedData.data as Category[];
      } else {
        throw new Error("Error fetching categories: " + parsedData.message);
      }
    } catch (error) {
      throw error;
    }
  },

  createCategory: async (name: string, description: string | null): Promise<Category> => {
    const response = await api.post<Category>("/categories", { name, description });
    return parseApiResponse<Category>(response.data);
  },

  updateCategory: async (id: number, name: string, description: string | null): Promise<Category> => {
    const response = await api.put<Category>(`/categories/${id}`, { name, description });
    return parseApiResponse<Category>(response.data);
  },

  deleteCategory: async (id: number): Promise<void> => {
    await api.delete(`/categories/${id}`);
  },
};
