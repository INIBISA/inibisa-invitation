<?php

namespace App\Support;

class WhatsAppInvitationMessage
{
    public const PLACEHOLDERS = ['{nama_tamu}', '{nama_mempelai}', '{tautan_undangan}'];

    public static function defaultTemplate(): string
    {
        return <<<'MESSAGE'
💌 *UNDANGAN PERNIKAHAN*

_Assalamu’alaikum Warahmatullahi Wabarakatuh._

Kepada Yth.
*{nama_tamu}*

Dengan penuh rasa syukur dan kebahagiaan, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk hadir dan memberikan doa restu pada acara pernikahan kami.

Merupakan suatu kebahagiaan dan kehormatan bagi kami apabila Bapak/Ibu/Saudara/i berkenan meluangkan waktu untuk hadir dan menjadi bagian dari momen istimewa dalam perjalanan hidup kami.

💍 *{nama_mempelai}*

Untuk informasi lengkap mengenai waktu, lokasi, serta rangkaian acara pernikahan kami, silakan membuka undangan digital melalui tautan berikut:

🔗 {tautan_undangan}

Kehadiran dan doa restu Anda merupakan hadiah yang sangat berarti bagi kami. Semoga hari bahagia ini menjadi awal dari perjalanan rumah tangga yang penuh cinta, keberkahan, dan kebahagiaan.

Atas perhatian, doa, dan kehadirannya, kami mengucapkan terima kasih yang sebesar-besarnya.

_Wassalamu’alaikum Warahmatullahi Wabarakatuh._

Salam hangat dan penuh cinta,
*{nama_mempelai}* 🤍
MESSAGE;
    }
}
