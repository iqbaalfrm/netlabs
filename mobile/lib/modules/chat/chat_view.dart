import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:lucide_icons_flutter/lucide_icons.dart';
import '../../app/theme/app_colors.dart';
import '../../app/constants/dummy_data.dart';
import 'chat_controller.dart';

class ChatView extends GetView<ChatController> {
  const ChatView({super.key});

  @override
  Widget build(BuildContext context) {
    if (!Get.isRegistered<ChatController>()) {
      Get.put(ChatController());
    }

    final tt = Theme.of(context).textTheme;

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        title: Row(children: [
          Container(
            width: 36, height: 36,
            decoration: BoxDecoration(
              color: AppColors.primary,
              borderRadius: BorderRadius.circular(16),
            ),
            child: const Icon(LucideIcons.bot, size: 18, color: Colors.white),
          ),
          const SizedBox(width: 10),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('AI Tutor', style: tt.bodyLarge),
              Row(children: [
                Container(width: 6, height: 6,
                  decoration: const BoxDecoration(
                    color: AppColors.success, shape: BoxShape.circle)),
                const SizedBox(width: 4),
                Text('Online', style: tt.labelSmall),
              ]),
            ],
          ),
        ]),
      ),
      body: Column(
        children: [
          Expanded(
            child: Obx(() => ListView.builder(
              controller: controller.scrollController,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
              itemCount: controller.daftarPesan.length +
                  (controller.aiMenulis.value ? 1 : 0),
              itemBuilder: (context, index) {
                if (index == controller.daftarPesan.length) {
                  return _buildTypingIndicator(tt);
                }
                final pesan = controller.daftarPesan[index];
                return _buildBubble(context, pesan['dariSiswa'], pesan['teks'], tt);
              },
            )),
          ),
          Obx(() => controller.daftarPesan.length <= 1
              ? _buildSuggestions(tt)
              : const SizedBox.shrink()),
          _buildInputBar(tt),
        ],
      ),
    );
  }

  Widget _buildBubble(BuildContext context, bool dariSiswa, String teks, TextTheme tt) {
    return Align(
      alignment: dariSiswa ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        constraints: BoxConstraints(
          maxWidth: MediaQuery.of(context).size.width * 0.75),
        decoration: BoxDecoration(
          color: dariSiswa ? AppColors.primary : AppColors.surface,
          borderRadius: BorderRadius.only(
            topLeft: const Radius.circular(16),
            topRight: const Radius.circular(16),
            bottomLeft: Radius.circular(dariSiswa ? 16 : 4),
            bottomRight: Radius.circular(dariSiswa ? 4 : 16),
          ),
          border: dariSiswa ? null : Border.all(color: AppColors.border),
        ),
        child: Text(teks,
          style: tt.bodyMedium?.copyWith(
            color: dariSiswa ? Colors.white : AppColors.textPrimary,
            height: 1.4)),
      ),
    );
  }

  Widget _buildTypingIndicator(TextTheme tt) {
    return Align(
      alignment: Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(16),
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
            Text('Mengetik...', style: tt.labelSmall),
          ],
        ),
      ),
    );
  }

  Widget _buildSuggestions(TextTheme tt) {
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
                color: AppColors.accent,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.border),
              ),
              child: Text(saran,
                style: tt.labelSmall?.copyWith(
                  color: AppColors.primary, fontWeight: FontWeight.w600)),
            ),
          );
        }).toList(),
      ),
    );
  }

  Widget _buildInputBar(TextTheme tt) {
    return Container(
      padding: const EdgeInsets.fromLTRB(16, 10, 16, 16),
      decoration: const BoxDecoration(
        color: AppColors.surface,
        border: Border(top: BorderSide(color: AppColors.border)),
      ),
      child: SafeArea(
        child: Row(children: [
          Expanded(
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              decoration: BoxDecoration(
                color: AppColors.background,
                borderRadius: BorderRadius.circular(16),
              ),
              child: TextField(
                controller: controller.inputController,
                style: tt.bodyMedium?.copyWith(color: AppColors.textPrimary),
                decoration: InputDecoration(
                  hintText: 'Tanya sesuatu...',
                  border: InputBorder.none,
                  hintStyle: tt.bodyMedium,
                  contentPadding: const EdgeInsets.symmetric(vertical: 12),
                ),
                onSubmitted: (_) => controller.kirimPesan(),
              ),
            ),
          ),
          const SizedBox(width: 10),
          GestureDetector(
            onTap: () => controller.kirimPesan(),
            child: Container(
              width: 44, height: 44,
              decoration: BoxDecoration(
                color: AppColors.primary,
                borderRadius: BorderRadius.circular(16),
              ),
              child: const Icon(LucideIcons.sendHorizontal,
                size: 18, color: Colors.white),
            ),
          ),
        ]),
      ),
    );
  }
}