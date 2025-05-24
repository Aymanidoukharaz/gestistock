import api from "@/lib/api";
import { StockMovement, StockMovementInput } from "@/types/stock-movement";
import { PaginationMeta } from "@/hooks/use-api-data"; // Import PaginationMeta

// Define a type for paginated responses
interface PaginatedResponse<T> {
  data: T[];
  meta: PaginationMeta;
}

// Helper function to parse response data
const parseApiResponse = <T>(data: any): T => {
  if (typeof data === 'string' && data.startsWith('+')) {
    try {
      return JSON.parse(data.substring(1)) as T;
    } catch (error) {
      console.error("Failed to parse API response:", error);
      throw new Error("Invalid JSON response from API after removing '+': " + (error as Error).message);
    }
  }
  // If data is already an object with data and meta (likely for paginated responses), return as is.
  if (typeof data === 'object' && data !== null && 'data' in data && 'meta' in data) {
    return data as T;
  }
  // Fallback for non-paginated or differently structured data
  return data as T;
};


export const stockMovementService = {
  getAll: async (params?: { page?: number }): Promise<PaginatedResponse<StockMovement>> => {
    const response = await api.get<any>("/stock-movements", { params });
    console.log("Stock movements raw response:", response.data);

    // The API is expected to return { data: [...], meta: {...} } when paginated
    const parsedData = parseApiResponse<PaginatedResponse<StockMovement>>(response.data);
    console.log("Stock movements parsed data:", parsedData);

    if (!parsedData || !Array.isArray(parsedData.data) || !parsedData.meta) {
      console.error("Invalid paginated response structure:", parsedData);
      // Fallback or throw error if structure is not as expected
      // For robustness, ensure a valid structure is returned or an error is clearly indicated.
      // This might involve returning a default PaginatedResponse structure on error
      // or re-throwing a more specific error.
      // For now, let's assume if it's not right, it's an error state.
      throw new Error("Invalid paginated response structure from stock movements API.");
    }
    
    return parsedData;
  },
  
  create: async (movement: StockMovementInput): Promise<StockMovement> => {
    const { productId, ...rest } = movement;
    const payload = {
      ...rest,
      product_id: productId, // Map to snake_case for the API
    };
    const response = await api.post<StockMovement>("/stock-movements", payload);
    // Assuming create might also be prefixed, adjust if necessary
    return parseApiResponse<StockMovement>(response.data);
  },
    getByProductId: async (productId: string): Promise<StockMovement[]> => {
    const response = await api.get<any>(`/stock-movements/product/${productId}`);
    console.log("Stock movements by product raw response:", response.data);
    
    const parsedData = parseApiResponse<any>(response.data);
    console.log("Stock movements by product parsed data:", parsedData);
    
    // Handle nested response structure like products
    const movements = parsedData?.data || parsedData;
    console.log("Stock movements by product final data:", movements);
    
    return Array.isArray(movements) ? movements : [];
  }
};
