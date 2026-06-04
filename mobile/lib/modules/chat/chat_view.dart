import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../app/theme/app_colors.dart';
import '../../app/constants/dummy_data.dart';
import 'chat_controller.dart';

// Halaman AI Chat — menggunakan GetView + ChatController
class ChatView extends GetView<ChatController> {
  const ChatView({super.key});

  @override
  Widget build(BuildContext context) {
    // Pastikan controller terdaftar
    if (!Get.isRegistered<ChatController>()) {
      Get.put(ChatController());
    }

    return Scaffold(
      backgroundColor: AppColors.bgLight,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        automaticallyImplyLeading: false,
        title: Row(
          children: [
            Container(
              width: 34, height: 34,
              decoration: BoxDecoration(
                color: AppColors.primary,
                borderRadius: BorderRadius.circular(10)),
              child: const Icon(LucideIcons.bot, size: 16, color: Colors.white),
            ),
            const SizedBox(width: 10),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('AI Tutor',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 14, fontWeight: FontWeight.w600,
                    color: AppColors.textPrimary)),
                Row(
                  children: [
                    Container(width: 6, height: 6,
                      decoration: const BoxDecoration(
                        color: Color(0xFF34C759), shape: BoxShape.circle)),
                    const SizedBox(width: 4),
                    Text('Online',
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 11, color: AppColors.textSecondary)),
                  ],
                ),
              ],
            ),
          ],
        ),
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(height: 1, color: AppColors.border),
        ),
      ),
      body: Column(
        children: [
          // Area chat
          Expanded(
            child: Obx(() => ListView.builder(
              controller: controller.scrollController,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
              itemCount: controller.daftarPesan.length +
                  (controller.aiMenulis.value ? 1 : 0),
              itemBuilder: (context, index) {
                if (index == controller.daftarPesan.length) {
                  return _buildTypingIndicator();
                }
                final pesan = controller.daftarPesan[index];
                return _buildBubble(context, pesan['dariSiswa'], pesan['teks']);
              },
            )),
          ),

          // Suggestion chips
          Obx(() => controller.daftarPesan.length <= 1
              ? _buildSuggestions()
              : const SizedBox.shrink()),

          // Input bar
          _buildInputBar(),
        ],
      ),
    );
  }

  // Bubble chat
  Widget _buildBubble(BuildContext context, bool dariSiswa, String teks) {
    return Align(
      alignment: dariSiswa ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
        constraints: BoxConstraints(
          maxWidth: MediaQuery.of(context).size.width * 0.75),
        decoration: BoxDecoration(
          color: dariSiswa ? AppColors.primary : Colors.white,
          borderRadius: BorderRadius.only(
            topLeft: const Radius.circular(14),
            topRight: const Radius.circular(14),
            bottomLeft: Radius.circular(dariSiswa ? 14 : 4),
            bottomRight: Radius.circular(dariSiswa ? 4 : 14),
          ),
          border: dariSiswa ? null : Border.all(color: AppColors.border),
        ),
        child: Text(teks,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 13,
            color: dariSiswa ? Colors.white : AppColors.textPrimary,
            height: 1.4)),
      ),
    );
  }

  // Typing indicator
  Widget _buildTypingIndicator() {
    return Align(
      alignment: Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: AppColors.border),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            const SizedBox(
              width: 16, height: 16,
              child: CircularProgressIndicator(
                strokeWidth: 2, color: AppColors.primary),
            ),
            const SizedBox(width: 8),
            Text('Mengetik...',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 12, color: AppColors.textSecondary)),
          ],
        ),
      ),
    );
  }

  // Suggestion chips
  Widget _buildSuggestions() {
    return Container(
      padding: const EdgeInsets.fromLTRB(16, 0, 16, 8),
      child: Wrap(
        spacing: 8,
        runSpacing: 8,
        children: DummyData.chatSuggestions.map((saran) {
          return GestureDetector(
            onTap: () => controller.kirimPesan(saran),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: AppColors.border)),
              child: Text(saran,
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 12, color: AppColors.primary)),
            ),
          );
        }).toList(),
      ),
    );
  }

  // Input bar
  Widget _buildInputBar() {
    return Container(
      padding: const EdgeInsets.fromLTRB(16, 10, 16, 16),
      decoration: BoxDecoration(
        color: Colors.white,
        border: Border(top: BorderSide(color: AppColors.border)),
      ),
      child: SafeArea(
        child: Row(
          children: [
            Expanded(
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 14),
                decoration: BoxDecoration(
                  color: AppColors.bgLight,
                  borderRadius: BorderRadius.circular(10)),
                child: TextField(
                  controller: controller.inputController,
                  style: GoogleFonts.plusJakartaSans(fontSize: 13),
                  decoration: InputDecoration(
                    hintText: 'Tanya sesuatu...',
                    hintStyle: GoogleFonts.plusJakartaSans(
                      fontSize: 13, color: AppColors.textSecondary),
                    border: InputBorder.none,
                    contentPadding: const EdgeInsets.symmetric(vertical: 12)),
                  onSubmitted: (_) => controller.kirimPesan(),
                ),
              ),
            ),
            const SizedBox(width: 10),
            GestureDetector(
              onTap: () => controller.kirimPesan(),
              child: Container(
                width: 40, height: 40,
                decoration: BoxDecoration(
                  color: AppColors.primary,
                  borderRadius: BorderRadius.circular(10)),
                child: const Icon(LucideIcons.sendHorizontal,
                    size: 18, color: Colors.white),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
