<style>
.table {
	width: 100%;
	margin-bottom: 20px;
	border: 1px solid #dddddd;
	border-collapse: collapse; 
}
.table th {
	font-weight: bold;
	padding: 5px;
	background: #efefef;
	border: 1px solid #dddddd;
}
.table td {
	border: 1px solid #dddddd;
	padding: 5px;
}

hr {
    margin: 15px 0px 15px 0;
    height: 0px;
    border: none;
    border-top: 1px solid #dddddd;
}

h1 {
    margin-bottom: 15px;
}

</style>

<?

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

CModule::IncludeModule('intranet');

$userIds = [182, 249, 12, 324, 350, 309, 170, 162, 306, 315, 314, 222, 56, 348]; // ID выбранных сотрудников

$arUsers = [];
foreach ($userIds as $userId) {
    $arUser = CUser::GetByID($userId)->Fetch();
    if ($arUser) {
        $arUsers[$arUser['ID']] = $arUser;
    }
}
// Заголовок
?>

<h1>Таблица просроченных задач сотрудников</h1>

<hr />

<?
// Выводим список сотрудников в виде таблицы
echo '<table class="table">
        <tr>
            <th>Имя сотрудника</th>
            <th>Просроченные задачи</th>
        </tr>';

foreach ($arUsers as $userId => $arUser) {
    echo '<tr>';
    // Добавляем ссылку на профиль сотрудника
    echo '<td><a href="/company/personal/user/'.$userId.'/">'.$arUser['LAST_NAME'].' '.$arUser['NAME'].'</a></td>';

    // Получаем просроченные задачи текущего сотрудника
    $dbOverdueTasks = CTasks::GetList([], [
        'RESPONSIBLE_ID' => $userId,
        'CHECK_PERMISSIONS' => 'N',
        'STATUS' => [CTasks::STATE_PENDING, CTasks::METASTATE_EXPIRED],
        '<DEADLINE' => ConvertTimeStamp(false, 'FULL')
    ]);

    echo '<td><ul>';
    while ($overdueTask = $dbOverdueTasks->Fetch()) {
        $taskLink = '/company/personal/user/' . $userId . '/tasks/task/view/' . $overdueTask['ID'] . '/';
        echo '<li><a href="' . $taskLink . '">' . $overdueTask['TITLE'] . '</a> (Просрочено: ' . $overdueTask['DEADLINE'] . ')</li>';
    }
    echo '</ul></td>';

    echo '</tr>';
}

echo '</table>';

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>