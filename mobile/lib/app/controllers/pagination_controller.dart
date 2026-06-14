import 'package:flutter/material.dart';
import 'package:get/get.dart';

typedef FetchPage<T> = Future<Map<String, dynamic>> Function(int page, int limit);

class PaginationController<T> extends GetxController {
  final FetchPage<T> fetchPage;
  final T Function(Map<String, dynamic>) fromJson;
  final int limit;

  PaginationController({
    required this.fetchPage,
    required this.fromJson,
    this.limit = 10,
  });

  final RxList<T> items = <T>[].obs;
  final RxInt currentPage = 0.obs;
  final RxBool isLoading = false.obs;
  final RxBool isLoadingMore = false.obs;
  final RxBool hasNext = true.obs;
  final RxString errorMessage = ''.obs;
  final RxInt total = 0.obs;

  ScrollController scrollController = ScrollController();

  @override
  void onInit() {
    super.onInit();
    scrollController.addListener(_onScroll);
    loadFirstPage();
  }

  @override
  void onClose() {
    scrollController.dispose();
    super.onClose();
  }

  void _onScroll() {
    if (!scrollController.hasClients) return;
    final maxScroll = scrollController.position.maxScrollExtent;
    final currentScroll = scrollController.position.pixels;

    if (currentScroll >= maxScroll * 0.8 && !isLoadingMore.value && hasNext.value) {
      loadNextPage();
    }
  }

  Future<void> loadFirstPage() async {
    isLoading.value = true;
    errorMessage.value = '';
    currentPage.value = 1;
    items.clear();

    try {
      final result = await fetchPage(1, limit);
      final List dataList = result['data'] ?? [];
      final meta = result['meta'] as Map<String, dynamic>?;

      items.assignAll(dataList.map((e) => fromJson(e as Map<String, dynamic>)));

      if (meta != null) {
        hasNext.value = meta['has_next'] ?? false;
        total.value = meta['total'] ?? 0;
      } else {
        hasNext.value = dataList.length >= limit;
      }
    } catch (e) {
      errorMessage.value = 'Gagal memuat data';
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> loadNextPage() async {
    if (isLoadingMore.value || !hasNext.value) return;

    isLoadingMore.value = true;
    currentPage.value++;

    try {
      final result = await fetchPage(currentPage.value, limit);
      final List dataList = result['data'] ?? [];
      final meta = result['meta'] as Map<String, dynamic>?;

      items.addAll(dataList.map((e) => fromJson(e as Map<String, dynamic>)));

      if (meta != null) {
        hasNext.value = meta['has_next'] ?? false;
        total.value = meta['total'] ?? 0;
      } else {
        hasNext.value = dataList.length >= limit;
      }
    } catch (e) {
      currentPage.value--;
      Get.snackbar('Error', 'Gagal memuat data',
          backgroundColor: Colors.red.shade100, colorText: Colors.red.shade800);
    } finally {
      isLoadingMore.value = false;
    }
  }

  Future<void> refresh() async {
    await loadFirstPage();
  }

  Widget buildLoadingIndicator() {
    return Obx(() {
      if (isLoadingMore.value) {
        return const Padding(
          padding: EdgeInsets.symmetric(vertical: 16),
          child: Center(child: CircularProgressIndicator(strokeWidth: 2)),
        );
      }
      if (!hasNext.value && items.isNotEmpty) {
        return const Padding(
          padding: EdgeInsets.symmetric(vertical: 16),
          child: Center(
            child: Text('Semua data sudah dimuat',
                style: TextStyle(color: Colors.grey, fontSize: 13)),
          ),
        );
      }
      return const SizedBox.shrink();
    });
  }
}
