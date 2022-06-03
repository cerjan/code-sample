<?php

declare(strict_types=1);

namespace App\Repository;

use DateTime;
use Exception;

class ShipmentRepository extends BaseRepository
{
    private const FILTER_MAPPING = [
        'carrier' => 'c.code',
        'delivery_note' => 's.deliveryNote',
        'cod.currency' => 'JSON_EXTRACT(r.params, \'$.currency\')',
    ];

    public function availableForUse(string $deliveryNote): bool
    {
        return !$this->findOneBy(['deliveryNote' => $deliveryNote, 'cancelledAt' => null]);
    }

    public function inTransport(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.id')
            ->where('s.lastStatusCheck IS NOT NULL OR s.createdAt < :olderThan')
            ->andWhere('s.registeredAt IS NOT NULL')
            ->andWhere('s.closedAt IS NULL')
            ->andWhere('s.cancelledAt IS NULL')
            ->setParameters(['olderThan' => (new DateTime())->modify('-1 DAY')])
            ->getQuery()->getSingleColumnResult();
    }

    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('s')
            ->select('PARTIAL s.{id, deliveryNote}, PARTIAL c.{id, code}, PARTIAL p.{id, carrierParcelNumber}, PARTIAL r.{id, code, params}')
            ->join('s.carrier', 'c')
            ->leftJoin('s.packages', 'p')
            ->leftJoin('s.services', 'r');

        foreach ($filters as $column => $filter) {
            if (!isset(self::FILTER_MAPPING[$column])) {
                throw new Exception(sprintf('Undefined column name `%s` for mapping.', $column));
            }

            $ors = [];
            foreach ($filter as $expr => $value) {
                $ors[] = $qb->expr()->{$expr}(self::FILTER_MAPPING[$column], $qb->expr()->literal($value));
            }
            $qb->andWhere($qb->expr()->orX(...$ors));
        }

        return $qb->getQuery()->getArrayResult();
    }
}