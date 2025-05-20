"use client"

import type React from "react"

import { ThemeProvider } from "@/components/theme-provider"
import { AuthProvider } from "@/hooks/use-auth"
import { MockDataProvider } from "@/hooks/use-mock-data"

export function Providers({ children }: { children: React.ReactNode }) {
  return (
    <ThemeProvider attribute="class" defaultTheme="light" enableSystem>
      <AuthProvider>
        <MockDataProvider>{children}</MockDataProvider>
      </AuthProvider>
    </ThemeProvider>
  )
}
