import api from "@/lib/api";
import { Supplier } from "@/types/supplier";

export const supplierService = {
  getAll: async (): Promise<Supplier[]> => {
    const response = await api.get<Supplier[]>("/suppliers");
    return response.data;
  },
  
  getById: async (id: string): Promise<Supplier> => {
    const response = await api.get<Supplier>(`/suppliers/${id}`);
    return response.data;
  },
  
  create: async (supplier: Omit<Supplier, "id">): Promise<Supplier> => {
    const response = await api.post<Supplier>("/suppliers", supplier);
    return response.data;
  },
  
  update: async (supplier: Supplier): Promise<Supplier> => {
    const response = await api.put<Supplier>(`/suppliers/${supplier.id}`, supplier);
    return response.data;
  },
  
  delete: async (id: string): Promise<void> => {
    await api.delete(`/suppliers/${id}`);
  }
};
