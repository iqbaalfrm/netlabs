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
                return _buildBubble(context, index, pesan, tt);
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

  Widget _buildBubble(BuildContext context, int index, Map<String, dynamic> pesan, TextTheme tt) {
    final dariSiswa = pesan['dariSiswa'] ?? false;
    final tipe = pesan['tipe'] ?? 'teks';

    if (tipe == 'file') {
      return _buildFileBubble(context, dariSiswa, pesan['namaFile'] ?? '', pesan['ukuranFile'] ?? '', tt);
    } else if (tipe == 'audio') {
      return _buildAudioBubble(context, index, dariSiswa, pesan['durasi'] ?? '', pesan['isPlaying'] ?? false, tt);
    }

    final teks = pesan['teks'] ?? '';
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

  Widget _buildFileBubble(BuildContext context, bool dariSiswa, String nama, String ukuran, TextTheme tt) {
    return Align(
      alignment: dariSiswa ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
        constraints: BoxConstraints(
          maxWidth: MediaQuery.of(context).size.width * 0.75),
        decoration: BoxDecoration(
          color: dariSiswa ? AppColors.primary : AppColors.surface,
          borderRadius: BorderRadius.circular(16),
          border: dariSiswa ? null : Border.all(color: AppColors.border),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: dariSiswa ? Colors.white.withOpacity(0.2) : AppColors.accent,
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(
                nama.toLowerCase().endsWith('.png') || nama.toLowerCase().endsWith('.jpg')
                    ? LucideIcons.image
                    : LucideIcons.fileText,
                color: dariSiswa ? Colors.white : AppColors.primary,
                size: 20,
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    nama,
                    style: tt.bodyMedium?.copyWith(
                      color: dariSiswa ? Colors.white : AppColors.textPrimary,
                      fontWeight: FontWeight.bold,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 2),
                  Text(
                    ukuran,
                    style: tt.labelSmall?.copyWith(
                      color: dariSiswa ? Colors.white70 : AppColors.textSecondary,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(width: 10),
            Icon(
              LucideIcons.checkCheck,
              color: dariSiswa ? Colors.white70 : AppColors.primary,
              size: 16,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAudioBubble(BuildContext context, int index, bool dariSiswa, String durasi, bool isPlaying, TextTheme tt) {
    return Align(
      alignment: dariSiswa ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
        constraints: BoxConstraints(
          maxWidth: MediaQuery.of(context).size.width * 0.75),
        decoration: BoxDecoration(
          color: dariSiswa ? AppColors.primary : AppColors.surface,
          borderRadius: BorderRadius.circular(16),
          border: dariSiswa ? null : Border.all(color: AppColors.border),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            GestureDetector(
              onTap: () => controller.togglePlayAudio(index),
              child: Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: dariSiswa ? Colors.white.withOpacity(0.2) : AppColors.accent,
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  isPlaying ? LucideIcons.pause : LucideIcons.play,
                  color: dariSiswa ? Colors.white : AppColors.primary,
                  size: 20,
                ),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: List.generate(
                      12,
                      (i) => Container(
                        width: 4,
                        height: (i % 3 + 2) * 4.0,
                        margin: const EdgeInsets.symmetric(horizontal: 1),
                        decoration: BoxDecoration(
                          color: isPlaying 
                              ? (dariSiswa ? Colors.white : AppColors.primary)
                              : (dariSiswa ? Colors.white54 : Colors.grey[300]),
                          borderRadius: BorderRadius.circular(2),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    isPlaying ? 'Playing...' : durasi,
                    style: tt.labelSmall?.copyWith(
                      color: dariSiswa ? Colors.white70 : AppColors.textSecondary,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(width: 10),
            Icon(
              LucideIcons.mic,
              color: dariSiswa ? Colors.white70 : AppColors.primary,
              size: 16,
            ),
          ],
        ),
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
          GestureDetector(
            onTap: () {
              Get.bottomSheet(
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: const BoxDecoration(
                    color: AppColors.surface,
                    borderRadius: BorderRadius.only(
                      topLeft: Radius.circular(20),
                      topRight: Radius.circular(20),
                    ),
                  ),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text('Unggah Berkas Jaringan', style: tt.titleMedium?.copyWith(fontWeight: FontWeight.bold)),
                      const SizedBox(height: 16),
                      ListTile(
                        leading: const Icon(LucideIcons.fileText, color: AppColors.primary),
                        title: const Text('Laporan Praktikum (.pdf)'),
                        subtitle: const Text('Simulasi kirim dokumen praktikum'),
                        onTap: () {
                          Get.back();
                          controller.simulasiUploadFile('laporan_praktikum_vlan.pdf', 'pdf', '1.2 MB');
                        },
                      ),
                      ListTile(
                        leading: const Icon(LucideIcons.image, color: AppColors.primary),
                        title: const Text('Gambar Topologi (.png)'),
                        subtitle: const Text('Simulasi kirim gambar topologi'),
                        onTap: () {
                          Get.back();
                          controller.simulasiUploadFile('topologi_jaringan_p1.png', 'png', '840 KB');
                        },
                      ),
                    ],
                  ),
                )
              );
            },
            child: Container(
              width: 44, height: 44,
              decoration: BoxDecoration(
                color: AppColors.accent,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.border),
              ),
              child: const Icon(LucideIcons.paperclip, size: 18, color: AppColors.primary),
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              decoration: BoxDecoration(
                color: AppColors.background,
                borderRadius: BorderRadius.circular(16),
              ),
              child: Obx(() => TextField(
                controller: controller.inputController,
                style: tt.bodyMedium?.copyWith(color: AppColors.textPrimary),
                enabled: !controller.isRecording.value,
                decoration: InputDecoration(
                  hintText: controller.isRecording.value 
                      ? '🎤 Merekam suara... Ketuk mic untuk kirim' 
                      : 'Tanya sesuatu...',
                  border: InputBorder.none,
                  hintStyle: tt.bodyMedium?.copyWith(
                    color: controller.isRecording.value ? Colors.red : null
                  ),
                  contentPadding: const EdgeInsets.symmetric(vertical: 12),
                ),
                onSubmitted: (_) => controller.kirimPesan(),
              )),
            ),
          ),
          const SizedBox(width: 10),
          Obx(() {
            final isTeksKosong = controller.inputTeks.value.trim().isEmpty;
            final isRec = controller.isRecording.value;
            
            return GestureDetector(
              onTap: () {
                if (isTeksKosong) {
                  controller.simulasiRecordAudio();
                } else {
                  controller.kirimPesan();
                }
              },
              child: Container(
                width: 44, height: 44,
                decoration: BoxDecoration(
                  color: isRec ? Colors.red : AppColors.primary,
                  borderRadius: BorderRadius.circular(16),
                  boxShadow: isRec ? [
                    BoxShadow(
                      color: Colors.red.withOpacity(0.4),
                      spreadRadius: 2,
                      blurRadius: 6,
                    )
                  ] : null,
                ),
                child: Icon(
                  isTeksKosong 
                      ? (isRec ? LucideIcons.micOff : LucideIcons.mic) 
                      : LucideIcons.sendHorizontal,
                  size: 18, 
                  color: Colors.white
                ),
              ),
            );
          }),
        ]),
      ),
    );
  }
}