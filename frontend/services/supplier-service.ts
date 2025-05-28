import api from "@/lib/api";
import { Supplier } from "@/types/supplier";

export const supplierService = {
  getAll: async (): Promise<Supplier[]> => {
    try {
      const response = await api.get<any>("/suppliers");
      const responseData = response.data;
  
      let dataToParse = responseData;
      if (typeof responseData === 'string' && responseData.startsWith('+')) {
        try {
          dataToParse = JSON.parse(responseData.substring(1));
        } catch (e) {
          dataToParse = null;
        }
      }
  
      if (Array.isArray(dataToParse)) {
        return dataToParse as Supplier[];
      }
      if (dataToParse && Array.isArray(dataToParse.data)) {
        return dataToParse.data as Supplier[];
      }
      if (dataToParse && Array.isArray(dataToParse.suppliers)) {
        return dataToParse.suppliers as Supplier[];
      }
      return [];
    } catch (error) {
      return [];
    }
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
    const endpoint = `/suppliers/${supplier.id}`;
    try {
      const response = await api.put<Supplier>(endpoint, supplier);
      return response.data;
    } catch (error) {
      throw error;
    }
  },
  
  delete: async (id: string): Promise<void> => {
    await api.delete(`/suppliers/${id}`);
  }
};
