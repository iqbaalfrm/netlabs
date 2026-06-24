import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

/// Warna utama aplikasi Netlabs
class AppColors {
  static const primary = Color(0xFF2D6A4F);
  static const secondary = Color(0xFF95D5B2);
  static const background = Color(0xFFF8F9FA);
  static const surface = Color(0xFFFFFFFF);
  static const textPrimary = Color(0xFF1A1A2E);
  static const textSecondary = Color(0xFF6C757D);
  static const border = Color(0xFFE9ECEF);
  static const error = Color(0xFFE63946);
  static const success = Color(0xFF40916C);

  // Turunan
  static const primaryLight = Color(0xFFF0FFF4);
  static const errorLight = Color(0xFFFEF2F2);
  static const warning = Color(0xFFF4A261);
  static const warningLight = Color(0xFFFFF7ED);
}

/// Tema gelap & terang — pakai Inter dari Google Fonts
class AppTheme {
  static ThemeData get light => ThemeData(
        useMaterial3: true,
        brightness: Brightness.light,
        scaffoldBackgroundColor: AppColors.background,
        colorScheme: const ColorScheme.light(
          primary: AppColors.primary,
          secondary: AppColors.secondary,
          surface: AppColors.surface,
          error: AppColors.error,
        ),
        textTheme: GoogleFonts.interTextTheme().copyWith(
          // Heading: w700, 22px  (headlineSmall)
          headlineSmall: GoogleFonts.inter(
            fontSize: 22, fontWeight: FontWeight.w700,
            color: AppColors.textPrimary,
          ),
          // Subheading: w600, 16px (titleMedium)
          titleMedium: GoogleFonts.inter(
            fontSize: 16, fontWeight: FontWeight.w600,
            color: AppColors.textPrimary,
          ),
          // Body: w400, 14px (bodyMedium)
          bodyMedium: GoogleFonts.inter(
            fontSize: 14, fontWeight: FontWeight.w400,
            color: AppColors.textPrimary, height: 1.5,
          ),
          // Caption: w400, 12px (labelSmall)
          labelSmall: GoogleFonts.inter(
            fontSize: 12, fontWeight: FontWeight.w400,
            color: AppColors.textSecondary,
          ),
          // Label besar (tombol)
          labelLarge: GoogleFonts.inter(
            fontSize: 14, fontWeight: FontWeight.w600,
            color: AppColors.textPrimary,
          ),
        ),
        appBarTheme: AppBarTheme(
          backgroundColor: AppColors.surface,
          foregroundColor: AppColors.textPrimary,
          elevation: 0,
          scrolledUnderElevation: 1,
          centerTitle: false,
          titleTextStyle: GoogleFonts.inter(
            fontSize: 18, fontWeight: FontWeight.w600,
            color: AppColors.textPrimary,
          ),
        ),
        elevatedButtonTheme: ElevatedButtonThemeData(
          style: ElevatedButton.styleFrom(
            backgroundColor: AppColors.primary,
            foregroundColor: Colors.white,
            minimumSize: const Size(double.infinity, 48),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(8),
            ),
            textStyle: GoogleFonts.inter(
              fontSize: 14, fontWeight: FontWeight.w600,
            ),
          ),
        ),
        outlinedButtonTheme: OutlinedButtonThemeData(
          style: OutlinedButton.styleFrom(
            foregroundColor: AppColors.primary,
            minimumSize: const Size(double.infinity, 48),
            side: const BorderSide(color: AppColors.primary),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(8),
            ),
            textStyle: GoogleFonts.inter(
              fontSize: 14, fontWeight: FontWeight.w600,
            ),
          ),
        ),
        inputDecorationTheme: InputDecorationTheme(
          filled: true,
          fillColor: AppColors.surface,
          contentPadding:
          const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(8),
            borderSide: const BorderSide(color: AppColors.border),
          ),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(8),
            borderSide: const BorderSide(color: AppColors.border),
          ),
          focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(8),
            borderSide: const BorderSide(color: AppColors.primary, width: 1.5),
          ),
          hintStyle: GoogleFonts.inter(
            fontSize: 14, color: AppColors.textSecondary,
          ),
        ),
        cardTheme: CardThemeData(
          color: AppColors.surface,
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
            side: const BorderSide(color: AppColors.border),
          ),
          margin: EdgeInsets.zero,
        ),
      );
}