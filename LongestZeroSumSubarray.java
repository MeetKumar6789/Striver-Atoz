import java.util.*;

public class LongestZeroSumSubarray {
    public static int maxLen(int[] arr) {
        Map<Integer, Integer> map = new HashMap<>();
        int sum = 0, maxLen = 0;
        for (int i = 0; i < arr.length; i++) {
            sum += arr[i];
            if (sum == 0) maxLen = i + 1;
            if (map.containsKey(sum)) {
                maxLen = Math.max(maxLen, i - map.get(sum));
            } else {
                map.put(sum, i);
            }
        }
        return maxLen;
    }

    public static void main(String[] args) {
        int[] arr1 = {15, -2, 2, -8, 1, 7, 10, 23};
        System.out.println(maxLen(arr1)); // 5

        int[] arr2 = {2, 10, 4};
        System.out.println(maxLen(arr2)); // 0
    }
}
