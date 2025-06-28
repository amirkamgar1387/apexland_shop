-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2025 at 06:18 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `apexland_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `full_name`, `email`, `photo`) VALUES
(5, 'user', 'pass', 'کاربر معمولی', 'user@example.com', 'uploads/admin/685e7a42bc375-admin.png');

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `name`, `slug`, `description`, `created_at`, `image`) VALUES
(2, 'راهنمای خرید و مقایسه', NULL, 'بررسی تخصصی و مقایسه انواع محصولات دیجیتال برای انتخاب بهتر و خرید هوشمندانه.', '2025-06-27 13:11:31', 'uploads/blog_categories/685e68d88dae5-راهنمای خرید و مقایسه.jpg'),
(3, 'اخبار تکنولوژی و نوآوری', NULL, 'جدیدترین اخبار و تحولات دنیای فناوری، معرفی محصولات و گجت‌های روز دنیا.', '2025-06-27 13:11:31', 'uploads/blog_categories/685e692b75aa6-اخبار تکنولوژی و نوآوری.jpg'),
(4, 'آموزش و ترفندهای کاربردی', NULL, 'آموزش‌های گام‌به‌گام، ترفندها و نکات کاربردی برای استفاده بهتر از محصولات دیجیتال.', '2025-06-27 13:11:31', 'uploads/blog_categories/685e699148877-آموزش و ترفندهای کاربردی.jpg'),
(5, 'بررسی تخصصی محصولات', NULL, 'نقد و بررسی عمیق و بی‌طرفانه انواع لپ‌تاپ، موبایل، لوازم جانبی و تجهیزات شبکه.', '2025-06-27 13:11:31', 'uploads/blog_categories/685e69b43dd15-بررسی تخصصی محصولات.jpg'),
(6, 'سبک زندگی دیجیتال', NULL, 'راهکارها و ایده‌هایی برای زندگی هوشمند، سلامت دیجیتال و استفاده بهینه از تکنولوژی.', '2025-06-27 13:11:31', 'uploads/blog_categories/685e69d059d4f-سبک زندگی دیجیتال.jpg'),
(7, 'امنیت و حریم خصوصی', NULL, 'آموزش و اخبار مرتبط با امنیت اطلاعات، محافظت از داده‌ها و حفظ حریم خصوصی در فضای مجازی.', '2025-06-27 13:11:31', 'uploads/blog_categories/685e69f832bb0-Security and privacy.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(220) DEFAULT NULL,
  `summary` varchar(400) DEFAULT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(20) DEFAULT 'draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `slug`, `summary`, `content`, `image`, `category_id`, `author`, `created_at`, `updated_at`, `status`) VALUES
(15, 'راهنمای خرید لپ‌تاپ مناسب برنامه‌نویسی در سال 2025', NULL, 'بررسی ویژگی‌های کلیدی لپ‌تاپ‌های مناسب برنامه‌نویسان و معرفی مدل‌های برتر سال 2025.', 'انتخاب لپ‌تاپ برای برنامه‌نویسی نیازمند توجه به پردازنده، رم، نمایشگر و عمر باتری است. در این مقاله مدل‌هایی مانند Dell XPS 15 و MacBook Pro M4 را با هم مقایسه می‌کنیم و مزایا و معایب هرکدام را بررسی می‌نماییم...', 'uploads/blog/685e7779a15e7-The best laptop for programming in 2025.jpg', 2, 'مدیر سایت', '2025-06-27 13:32:37', '2025-06-27 14:20:33', 'published'),
(16, 'مقایسه گوشی‌های پرچمدار 2025؛ آیفون یا سامسونگ؟', NULL, 'مقایسه تخصصی iPhone 15 Pro Max و Samsung Galaxy Z Fold6 از نظر دوربین، نمایشگر و عملکرد.', 'در این مقاله به بررسی تفاوت‌های کلیدی دو گوشی پرچمدار سال 2025 می‌پردازیم. از کیفیت دوربین و نمایشگر تا سرعت پردازنده و امکانات نرم‌افزاری، هر دو مدل را به‌صورت عملی مقایسه می‌کنیم...', 'uploads/blog/685e79569da7a-iPhone or Samsung.jpg', 2, 'مدیر سایت', '2025-06-27 13:32:37', '2025-06-27 14:28:30', 'published'),
(17, 'معرفی فناوری Wi-Fi 7؛ آینده اینترنت پرسرعت', NULL, 'بررسی قابلیت‌ها و مزایای Wi-Fi 7 و تاثیر آن بر تجربه کاربری تجهیزات شبکه.', 'Wi-Fi 7 با سرعت انتقال داده تا ۴ برابر بیشتر از نسل قبل، تاخیر کمتر و پایداری بالاتر، انقلابی در شبکه‌های خانگی و اداری ایجاد می‌کند. در این مقاله با ویژگی‌های فنی و کاربردهای این فناوری آشنا می‌شوید...', 'uploads/blog/685e79611c2f0-Wi-Fi 7 technology.jpg', 3, 'مدیر سایت', '2025-06-27 13:32:37', '2025-06-27 14:28:41', 'published'),
(18, 'رونمایی از نسل جدید کارت‌های گرافیک NVIDIA RTX 5090', NULL, 'بررسی مشخصات و قدرت پردازشی RTX 5090 و تاثیر آن بر دنیای گیمینگ و رندرینگ.', 'کارت گرافیک RTX 5090 با معماری جدید و حافظه ۲۴ گیگابایتی، مرزهای گرافیک را جابجا کرده است. در این مقاله به بررسی عملکرد، مصرف انرژی و قابلیت‌های ویژه این کارت می‌پردازیم...', 'uploads/blog/685e796bce519-NVIDIA RTX 5090.jpg', 3, 'مدیر سایت', '2025-06-27 13:32:37', '2025-06-27 14:28:51', 'published'),
(19, '۱۰ ترفند افزایش عمر باتری گوشی‌های هوشمند', NULL, 'راهکارهای ساده و کاربردی برای بهبود شارژدهی موبایل در زندگی روزمره.', 'با رعایت نکاتی مانند کاهش روشنایی نمایشگر، غیرفعال‌کردن GPS و مدیریت اپلیکیشن‌ها می‌توانید عمر باتری گوشی خود را به‌طور محسوسی افزایش دهید. در این مقاله ۱۰ ترفند مهم را آموزش می‌دهیم...', 'uploads/blog/685e7974a3519-Extending smartphone battery life.jpg', 4, 'مدیر سایت', '2025-06-27 13:32:37', '2025-06-27 14:29:00', 'published'),
(20, 'آموزش نصب و راه‌اندازی روترهای Wi-Fi 7', NULL, 'راهنمای گام‌به‌گام نصب و تنظیمات اولیه روترهای نسل جدید برای کاربران خانگی.', 'نصب روتر Wi-Fi 7 بسیار ساده است اما با رعایت چند نکته می‌توانید بهترین پوشش و سرعت را داشته باشید. در این مقاله مراحل نصب، انتخاب مکان مناسب و تنظیمات امنیتی را آموزش می‌دهیم...', 'uploads/blog/685e797dd3e12-Wi-Fi routers 7.jpg', 4, 'مدیر سایت', '2025-06-27 13:32:37', '2025-06-27 14:29:09', 'published'),
(21, 'بررسی تخصصی Apple Watch Series 10؛ بهترین ساعت هوشمند 2025؟', NULL, 'نقد و بررسی کامل اپل واچ سری ۱۰ از نظر طراحی، امکانات و عمر باتری.', 'اپل واچ سری ۱۰ با نمایشگر همیشه روشن، سنسور ECG و باتری سه‌روزه، یکی از کامل‌ترین ساعت‌های هوشمند بازار است. در این مقاله نقاط قوت و ضعف این ساعت را بررسی می‌کنیم...', 'uploads/blog/685e79893e368-Apple Watch Series 10.jpg', 5, 'مدیر سایت', '2025-06-27 13:32:37', '2025-06-27 14:29:21', 'published'),
(22, 'نقد و بررسی کیبورد Razer BlackWidow V5 برای گیمرها', NULL, 'بررسی عملکرد، کیفیت ساخت و امکانات کیبورد مکانیکی Razer BlackWidow V5.', 'این کیبورد با سوییچ‌های مکانیکی نسل جدید، نورپردازی RGB و کلیدهای ماکرو، تجربه‌ای بی‌نظیر برای گیمرها فراهم می‌کند. در این مقاله به جزئیات فنی و تجربه کاربری آن می‌پردازیم...', 'uploads/blog/685e799406ade-Razer BlackWidow keyboard.jpg', 5, 'مدیر سایت', '2025-06-27 13:32:37', '2025-06-27 14:29:32', 'published'),
(23, 'تاثیر گجت‌های هوشمند بر سلامت و سبک زندگی مدرن', NULL, 'بررسی نقش ساعت‌های هوشمند و گجت‌ها در پایش سلامت و بهبود کیفیت زندگی.', 'گجت‌های هوشمند مانند ساعت‌های اپل و سامسونگ با قابلیت‌هایی چون پایش ضربان قلب، خواب و فعالیت بدنی، به کاربران کمک می‌کنند سبک زندگی سالم‌تری داشته باشند...', 'uploads/blog/685e79a18bc29-The impact of smart gadgets on health and modern lifestyle.jpg', 6, 'مدیر سایت', '2025-06-27 13:32:37', '2025-06-27 14:29:45', 'published'),
(24, 'راهکارهای مدیریت زمان با اپلیکیشن‌های دیجیتال', NULL, 'معرفی بهترین اپلیکیشن‌های مدیریت زمان و افزایش بهره‌وری در سال 2025.', 'با استفاده از اپلیکیشن‌هایی مانند Todoist و Notion می‌توانید کارهای روزانه خود را بهتر مدیریت کنید. در این مقاله بهترین ابزارهای دیجیتال برای مدیریت زمان را معرفی می‌کنیم...', 'uploads/blog/685e79afb1d8a-Time management solutions with digital applications.jpg', 6, 'مدیر سایت', '2025-06-27 13:32:37', '2025-06-27 14:29:59', 'published'),
(25, 'راهنمای افزایش امنیت اطلاعات شخصی در فضای مجازی', NULL, 'آموزش نکات کلیدی برای محافظت از داده‌ها و جلوگیری از هک شدن حساب‌ها.', 'استفاده از رمزهای عبور قوی، فعال‌سازی تایید دو مرحله‌ای و به‌روزرسانی نرم‌افزارها از مهم‌ترین راهکارهای افزایش امنیت هستند. در این مقاله این نکات را به‌صورت عملی آموزش می‌دهیم...', 'uploads/blog/685e79baba2c5-Increasing the security of personal information in cyberspace.jpg', 7, 'مدیر سایت', '2025-06-27 13:32:37', '2025-06-27 14:30:10', 'published'),
(26, 'بررسی تهدیدات جدید بدافزاری در سال 2025', NULL, 'معرفی انواع بدافزارهای جدید و روش‌های مقابله با آن‌ها برای کاربران خانگی و سازمانی.', 'با گسترش اینترنت اشیا و ابزارهای هوشمند، تهدیدات بدافزاری نیز پیچیده‌تر شده‌اند. در این مقاله به معرفی بدافزارهای جدید و راه‌های مقابله با آن‌ها می‌پردازیم...', 'uploads/blog/685e79c4235c3-New malware threats in 2025.png', 7, 'مدیر سایت', '2025-06-27 13:32:37', '2025-06-27 14:30:20', 'published');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `created_at`) VALUES
(15, 'لپ تاپ و الترابوک', 'جدیدترین مدل‌های لپ‌تاپ، الترابوک و نوت‌بوک مناسب کار، تحصیل و بازی با بهترین برندها و گارانتی معتبر.', 'uploads/categories/685e53715c5e9-Laptops and Ultrabooks.jpg', '2025-06-27 07:59:29'),
(16, 'موبایل و تبلت', 'انواع گوشی موبایل و تبلت از برندهای معتبر جهانی با گارانتی و قیمت رقابتی، مناسب برای همه سلیقه‌ها.', 'uploads/categories/685e548c5ecf7-Mobile and tablet.jpeg', '2025-06-27 07:59:29'),
(17, 'قطعات کامپیوتر', 'کارت گرافیک، مادربرد، رم، پردازنده و سایر قطعات سخت‌افزاری برای ارتقاء و اسمبل سیستم‌های حرفه‌ای و گیمینگ.', 'uploads/categories/685e54971787d-Computer parts.jpg', '2025-06-27 07:59:29'),
(18, 'لوازم جانبی دیجیتال', 'انواع لوازم جانبی شامل هدفون، موس، کیبورد، پاوربانک، کابل و شارژر با کیفیت عالی و قیمت مناسب.', 'uploads/categories/685e549f67487-Digital accessories.jpg', '2025-06-27 07:59:29'),
(19, 'گجت و ساعت هوشمند', 'جدیدترین گجت‌ها و ساعت‌های هوشمند برای زندگی مدرن، پایش سلامت و تناسب اندام.', 'uploads/categories/685e54aa2f366-Gadgets and smartwatches.jpg', '2025-06-27 07:59:29'),
(20, 'تجهیزات شبکه و ذخیره‌سازی', 'مودم، روتر، هارد اکسترنال، فلش مموری و تجهیزات ذخیره‌سازی و شبکه برای خانه و محل کار.', 'uploads/categories/685e54b2773bb-Network and storage equipment.jpg', '2025-06-27 07:59:29');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `product_id`, `post_id`, `parent_id`, `name`, `content`, `created_at`, `status`) VALUES
(16, 29, NULL, NULL, 'امیر', 'عالی بود تشکر از شما', '2025-06-27 13:56:11', 'approved'),
(17, NULL, 15, NULL, 'امیر', 'محتوای بسیار عالی', '2025-06-27 13:56:25', 'approved'),
(18, 22, NULL, NULL, 'امیر', 'عالای', '2025-06-27 14:44:18', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `full_description` longtext DEFAULT NULL,
  `price_usd` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `short_description`, `full_description`, `price_usd`, `image`, `category_id`, `stock`, `brand`, `features`, `created_at`) VALUES
(20, 'Dell XPS 15 2025', 'لپ‌تاپ حرفه‌ای 2025 با نمایشگر OLED و طراحی فوق‌العاده باریک.', 'Dell XPS 15 2025 با نمایشگر 15.6 اینچی OLED، وضوح 4K و روشنایی 600 نیت، تجربه بصری بی‌نظیری را ارائه می‌دهد. این مدل مجهز به پردازنده نسل ۱۴ اینتل Core i9، رم 32 گیگابایت DDR5 و حافظه SSD با ظرفیت 1 ترابایت است. بدنه آلومینیومی، وزن سبک و باتری با شارژدهی 12 ساعته، این لپ‌تاپ را برای برنامه‌نویسان، طراحان و کاربران حرفه‌ای ایده‌آل می‌کند. کارت گرافیک NVIDIA RTX 4070 امکان اجرای سنگین‌ترین نرم‌افزارها و بازی‌ها را فراهم می‌سازد.', '2100.00', 'uploads/products/685e7d545c619-Dell XPS 15.jpg', 15, 10, 'Dell', 'پردازنده i9-14900H، رم 32GB DDR5، SSD 1TB NVMe، گرافیک RTX 4070، نمایشگر OLED 4K، Thunderbolt 4', '2025-06-27 08:30:00'),
(21, 'Apple MacBook Pro M4 2025', 'مک‌بوک پرو 2025 با چیپ M4 و نمایشگر Mini-LED، مناسب حرفه‌ای‌ها.', 'Apple MacBook Pro M4 2025 با چیپ قدرتمند M4 و معماری ۳ نانومتری، سرعت و کارایی بی‌نظیری را ارائه می‌دهد. نمایشگر 16 اینچی Mini-LED با روشنایی 1600 نیت، دقت رنگ فوق‌العاده و نرخ تازه‌سازی 120 هرتز، این دستگاه را برای تدوین ویدیو، طراحی گرافیک و برنامه‌نویسی پیشرفته ایده‌آل می‌کند. باتری با شارژدهی 18 ساعته، سیستم صوتی شش‌کاناله و بدنه آلومینیومی یکپارچه از دیگر ویژگی‌های این مدل است.', '2500.00', 'uploads/products/685e65e923f2d-Apple MacBook Pro M4.jpeg', 15, 8, 'Apple', 'چیپ M4، رم 32GB، SSD 2TB، نمایشگر Mini-LED، Thunderbolt 4، باتری 18 ساعته', '2025-06-27 08:31:00'),
(22, 'iPhone 15 Pro Max 2025', 'جدیدترین آیفون 2025 با دوربین ۵۰ مگاپیکسل و بدنه تیتانیومی.', 'iPhone 15 Pro Max 2025 با دوربین سه‌گانه ۵۰ مگاپیکسلی، قابلیت فیلم‌برداری 8K و سنسور LiDAR، تجربه عکاسی و فیلم‌برداری حرفه‌ای را به ارمغان می‌آورد. نمایشگر Super Retina XDR با نرخ تازه‌سازی 120 هرتز، پردازنده A19 Bionic و پشتیبانی از 5G، این گوشی را به یکی از سریع‌ترین و پیشرفته‌ترین موبایل‌های جهان تبدیل کرده است. بدنه تیتانیومی، مقاومت در برابر آب و شارژ سریع 50 واتی از دیگر ویژگی‌های این مدل است.', '1800.00', 'uploads/products/685e65c6adefe-iPhone 15 Pro Max.png', 16, 15, 'Apple', 'دوربین ۵۰MP، فیلم‌برداری 8K، نمایشگر 120Hz، پردازنده A19، شارژ سریع 50W', '2025-06-27 08:32:00'),
(23, 'Samsung Galaxy Z Fold6 2025', 'گوشی تاشو 2025 سامسونگ با نمایشگر Dynamic AMOLED و قلم S Pen.', 'Samsung Galaxy Z Fold6 2025 با نمایشگر 7.6 اینچی Dynamic AMOLED 2X، قابلیت تاشدن و پشتیبانی از قلم S Pen، تجربه‌ای نوین از کار با موبایل را ارائه می‌دهد. این گوشی مجهز به پردازنده Snapdragon 8 Gen4، رم 16 گیگابایت و حافظه داخلی 1 ترابایت است. دوربین سه‌گانه 108 مگاپیکسلی، باتری 5000 میلی‌آمپرساعتی و مقاومت در برابر آب و گرد و غبار (IP68) از دیگر ویژگی‌های این مدل است.', '2000.00', 'uploads/products/685e659bdc26d-Samsung Galaxy Z Fold6.jpg', 16, 12, 'Samsung', 'نمایشگر Dynamic AMOLED 2X، قلم S Pen، رم 16GB، حافظه 1TB، دوربین 108MP', '2025-06-27 08:33:00'),
(24, 'NVIDIA RTX 5090 24GB 2025', 'کارت گرافیک نسل جدید 2025 با قدرت بی‌نظیر برای گیمینگ و رندرینگ.', 'NVIDIA RTX 5090 با حافظه 24 گیگابایت GDDR7 و معماری Ada Lovelace، بالاترین قدرت پردازشی را برای بازی‌های 8K و رندرینگ سه‌بعدی فراهم می‌کند. پشتیبانی از Ray Tracing نسل سوم، DLSS 4.0 و خروجی تصویر HDMI 2.2 این کارت را به انتخاب اول گیمرها و طراحان حرفه‌ای تبدیل کرده است. سیستم خنک‌کننده سه‌فن و مصرف انرژی بهینه از دیگر مزایای این مدل است.', '3200.00', 'uploads/products/685e6541dc4f8-NVIDIA RTX 5090 24GB.jpg', 17, 5, 'NVIDIA', '24GB GDDR7، Ray Tracing 3، DLSS 4.0، HDMI 2.2، PCIe 5.0', '2025-06-27 08:34:00'),
(25, 'Intel Core i9-15900K 2025', 'پردازنده نسل ۱۵ اینتل با ۲۴ هسته و ۳۲ رشته.', 'Intel Core i9-15900K با ۲۴ هسته و ۳۲ رشته، فرکانس پایه 4.2 گیگاهرتز و فناوری Intel Turbo Boost Max 3.0، مناسب‌ترین انتخاب برای سیستم‌های گیمینگ و ورک‌استیشن‌های حرفه‌ای است. پشتیبانی از PCIe 5.0، حافظه DDR5 و مصرف انرژی بهینه، این پردازنده را به یکی از سریع‌ترین و کارآمدترین پردازنده‌های سال 2025 تبدیل کرده است.', '900.00', 'uploads/products/685e650322748-Intel Core i9-15900K.jpg', 17, 10, 'Intel', '۲۴ هسته، ۳۲ رشته، PCIe 5.0، DDR5، Turbo Boost 3.0', '2025-06-27 08:35:00'),
(26, 'Logitech MX Master 4 2025', 'ماوس بی‌سیم نسل جدید با ارگونومی عالی و دقت فوق‌العاده.', 'Logitech MX Master 4 با طراحی ارگونومیک، سنسور 8000DPI و قابلیت اتصال به سه دستگاه همزمان، انتخابی عالی برای کاربران حرفه‌ای و طراحان است. این ماوس با باتری قابل شارژ تا 70 روز کار می‌کند و از طریق USB-C شارژ می‌شود. اسکرول هوشمند، کلیدهای قابل برنامه‌ریزی و سازگاری با ویندوز و مک از دیگر ویژگی‌های این مدل است.', '150.00', 'uploads/products/685e643c661d2-Logitech MX Master 4.jpg', 18, 20, 'Logitech', 'سنسور 8000DPI، اتصال چندگانه، USB-C، باتری 70 روزه', '2025-06-27 08:36:00'),
(27, 'Razer BlackWidow V5 2025', 'کیبورد مکانیکی گیمینگ با نورپردازی RGB و سوییچ‌های نسل جدید.', 'Razer BlackWidow V5 با سوییچ‌های مکانیکی نسل جدید، نورپردازی RGB با ۱۶.۸ میلیون رنگ و نرم‌افزار اختصاصی Razer Synapse، تجربه تایپ و بازی بی‌نظیری را ارائه می‌دهد. این کیبورد دارای کلیدهای ماکرو، استراحتگاه مچ مغناطیسی و بدنه مقاوم در برابر پاشش آب است.', '180.00', 'uploads/products/685e63ec40829-Razer BlackWidow V5.jpg', 18, 15, 'Razer', 'سوییچ مکانیکی، RGB، کلید ماکرو، استراحتگاه مچ', '2025-06-27 08:37:00'),
(28, 'Apple Watch Series 10 2025', 'ساعت هوشمند نسل ۱۰ اپل با پایش سلامت و نمایشگر همیشه روشن.', 'Apple Watch Series 10 با نمایشگر همیشه روشن Retina، سنسور ECG، پایش اکسیژن خون و قابلیت پایش خواب، یکی از کامل‌ترین ساعت‌های هوشمند سال 2025 است. این مدل با باتری ۳ روزه، مقاومت در برابر آب تا عمق ۵۰ متر و بندهای متنوع عرضه می‌شود. پشتیبانی از تماس و پیامک مستقل و اپلیکیشن‌های ورزشی از دیگر ویژگی‌های این ساعت است.', '700.00', 'uploads/products/685e63dfc29e1-Apple Watch Series 10.png', 19, 18, 'Apple', 'ECG، پایش اکسیژن، باتری ۳ روزه، ضدآب ۵۰ متر', '2025-06-27 08:38:00'),
(29, 'Samsung Galaxy Watch 7 2025', 'ساعت هوشمند 2025 سامسونگ با پایش خواب و GPS داخلی.', 'Samsung Galaxy Watch 7 با نمایشگر Super AMOLED، GPS داخلی، سنسور پایش فشار خون و قابلیت شارژ سریع، انتخابی عالی برای ورزشکاران و کاربران روزمره است. این ساعت با باتری ۴ روزه، مقاومت در برابر آب و گرد و غبار و پشتیبانی از بیش از ۱۰۰ حالت ورزشی عرضه می‌شود.', '600.00', 'uploads/products/685e63031f88c-Samsung Galaxy Watch 7.png', 19, 16, 'Samsung', 'GPS، پایش فشار خون، باتری ۴ روزه، Super AMOLED', '2025-06-27 08:39:00'),
(30, 'WD Black SN950X 2TB 2025', 'اس‌اس‌دی NVMe پرسرعت با ظرفیت ۲ ترابایت و نسل پنجم.', 'WD Black SN950X با ظرفیت ۲ ترابایت و سرعت خواندن ۷۵۰۰MB/s، مناسب‌ترین انتخاب برای گیمرها و کاربران حرفه‌ای است. این SSD از رابط PCIe Gen5 پشتیبانی می‌کند و با فناوری Heatsink دمای خود را کنترل می‌کند. مقاومت بالا در برابر شوک و مصرف انرژی پایین از دیگر مزایای این مدل است.', '350.00', 'uploads/products/685e62c32e1fa-WD Black SN950X 2TB 2.jpg', 20, 25, 'Western Digital', 'NVMe Gen5، سرعت ۷۵۰۰MB/s، Heatsink', '2025-06-27 08:40:00'),
(31, 'TP-Link Archer AX12000 2025', 'روتر وای‌فای 7 فوق‌سریع با ۱۲ آنتن و پشتیبانی از Wi-Fi 7.', 'TP-Link Archer AX12000 با پشتیبانی از Wi-Fi 7، سرعت انتقال داده تا ۱۲ گیگابیت بر ثانیه و ۱۲ آنتن قدرتمند، پوشش بی‌نظیری را برای خانه و محل کار فراهم می‌کند. این روتر دارای پورت‌های 10G، فناوری OFDMA و امنیت WPA3 است. مدیریت هوشمند شبکه و اپلیکیشن موبایل از دیگر ویژگی‌های این مدل است.', '400.00', 'uploads/products/685e619e28052-TP-Link Archer AX12000.jpg', 20, 10, 'TP-Link', 'Wi-Fi 7، ۱۲ آنتن، پورت 10G، WPA3', '2025-06-27 08:41:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comments_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
