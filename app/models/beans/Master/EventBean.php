<?php

namespace Beans\MasterData;

Class EventBean extends BaseMasterDataBean
{

    use \Traits\BeanParts\EventId;
    use \Traits\BeanParts\EventType;
    use \Traits\BeanParts\Name;
    use \Traits\BeanParts\StartTime;
    use \Traits\BeanParts\EndTime;

    use \Traits\BeanLogics\StartTimeAndEndTime;

}