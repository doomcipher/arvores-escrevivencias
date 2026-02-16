USE arvores;

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

INSERT INTO `alunos` (`id_alunos`, `matricula`, `nome`) VALUES
(10, '20241024010003', 'Arthur Vieira');

INSERT INTO `comentarios` (`id_comentarios`, `id_postagem`, `id_alunos`, `comentarios`, `data`, `confirmador`, `slide_index`) VALUES
(320, 40, 10, 'dasdasdasdasd', '2025-12-12 00:23:56', 1, NULL),
(321, 41, 10, 'adsasdasdasdsa', '2025-12-12 00:24:00', 1, NULL),
(323, 41, 10, 'dasdasda', '2025-12-12 00:55:13', 1, NULL),
(324, 41, 10, 'sdkasnjdkljasdn', '2025-12-12 01:16:54', 1, NULL),
(325, 42, 10, 'dasdasd', '2025-12-13 00:25:46', 1, NULL),
(326, 43, 10, 'dasdasdasda', '2025-12-13 00:28:39', 1, NULL),
(327, 43, 10, 'u9iuoiuio', '2025-12-13 01:47:48', 1, NULL),
(328, 43, 10, 'asdasdasdasdsadasd', '2025-12-13 15:52:07', 1, NULL);

--
-- Extraindo dados da tabela `postagem`
--

INSERT INTO `postagem` (`id_postagem`, `id_professor`, `titulo`, `data`, `descricao`, `tipo_postagem`, `tema`, `link_midia`) VALUES
(40, 1, 'sASasS', '2025-12-12 00:22:47', 'dasdasdasd', 'video', 'educacao_antirracista', 'https://res.cloudinary.com/df9fueyfn/video/upload/v1765509773/posts/40/bff79lrhf7li1oz5ujsx.mp4'),
(41, 1, 'dasdasdasd', '2025-12-12 00:23:04', 'dasdasdasd', 'video', 'educacao_antirracista', 'https://res.cloudinary.com/df9fueyfn/video/upload/v1765509791/posts/41/go1ykxcdq8gmudgphbxp.mp4'),
(42, 1, 'adasdas', '2025-12-12 22:23:45', 'dsadasdas', 'imagem', 'educacao_antirracista', '[\"https:\\/\\/res.cloudinary.com\\/df9fueyfn\\/image\\/upload\\/v1765589035\\/posts\\/42\\/vqyje3v03dsw6glnnuh9.png\",\"https:\\/\\/res.cloudinary.com\\/df9fueyfn\\/image\\/upload\\/v1765589036\\/posts\\/42\\/zyeyehpm22eixcu8b0tp.png\",\"https:\\/\\/res.cloudinary.com\\/df9fueyfn\\/image\\/upload\\/v1765589037\\/posts\\/42\\/ximus4izgqt1qa5b1fmo.png\"]'),
(43, 1, 'dasdad', '2025-12-13 00:27:13', 'asdasd', 'imagem', 'educacao_antirracista', '[\"https:\\/\\/res.cloudinary.com\\/df9fueyfn\\/image\\/upload\\/v1765596439\\/posts\\/43\\/nlqc7yboa6yzpkiswtud.png\",\"https:\\/\\/res.cloudinary.com\\/df9fueyfn\\/image\\/upload\\/v1765596440\\/posts\\/43\\/eahpmrqnvlnbgmpi6uvt.png\",\"https:\\/\\/res.cloudinary.com\\/df9fueyfn\\/image\\/upload\\/v1765596442\\/posts\\/43\\/dtzbnlbvzqbrrnp5xdlq.png\",\"https:\\/\\/res.cloudinary.com\\/df9fueyfn\\/image\\/upload\\/v1765596443\\/posts\\/43\\/m4iwfchtyy2ykvjr9va4.png\"]'),
(45, 1, 'asdasdadas', '2025-12-13 17:00:31', 'dasdasdasd', 'video', 'esportes_representatividade', 'https://res.cloudinary.com/df9fueyfn/video/upload/v1765656039/posts/45/omz3mzplrbx50hirt96u.mp4');

-- --------------------------------------------------------

--
-- Estrutura da tabela `professores`

INSERT INTO `professores` (`id_professor`, `nome`, `matricula`) VALUES
(1, 'Arthur Vieira', '20241024010003');

SET FOREIGN_KEY_CHECKS=1;

COMMIT;