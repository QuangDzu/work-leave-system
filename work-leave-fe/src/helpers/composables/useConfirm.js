import Swal from "sweetalert2";

export const useConfirm = () => {
  const confirm = (options) => {
    return Swal.fire({
      title: options.title || "Xác nhận",
      text: options.message || "Bạn có chắc chắn?",
      icon: options.type === "danger" ? "warning" : "question",
      showCancelButton: true,
      confirmButtonColor: options.type === "danger" ? "#e11d48" : "#0284c7",
      cancelButtonColor: "#64748b",
      confirmButtonText: options.confirmText || "Đồng ý",
      cancelButtonText: options.cancelText || "Hủy bỏ",
      reverseButtons: true,
    }).then((result) => result.isConfirmed);
  };

  return { confirm };
};
